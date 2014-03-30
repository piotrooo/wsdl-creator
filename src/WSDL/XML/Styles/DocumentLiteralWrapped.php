<?php
/**
 * DocumentLiteralWrapped
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

use WSDL\Parser\MethodParser;
use WSDL\Types\Type;
use WSDL\Utilities\TypeHelper;

class DocumentLiteralWrapped extends Style
{
    public function bindingStyle()
    {
        return 'document';
    }

    public function bindingUse()
    {
        return 'literal';
    }

    public function methodInput(MethodParser $method)
    {
        return array($this->_createPart($method, 'Request'));
    }

    public function methodOutput(MethodParser $method)
    {
        return $this->_createPart($method, 'Response');
    }

    private function _createPart(MethodParser $method, $type = '')
    {
        $name = 'parameters';
        $elementName = $method->getName() . $type . 'Parameters';
        $element = array(
            'name' => $name,
            'element' => $elementName
        );
        return $element;
    }

    public function typeParameters(MethodParser $method)
    {
        $elements = array();
        foreach ($method->parameters() as $parameter) {
            $elements[] = $this->_generateTypeToDocumentWrapper($parameter, $method, 'Request');
        }
        return $elements;
    }

    public function typeReturning(MethodParser $method)
    {
        return $this->_generateTypeToDocumentWrapper($method->returning(), $method, 'Response');
    }

    public function _generateTypeToDocumentWrapper($parameter, MethodParser $method, $type)
    {
        if (TypeHelper::isSimple($parameter)) {
            $generateComplexType = $this->_generateDocumentSimpleType($method, $type);
            if ($generateComplexType) {
                return $generateComplexType;
            }
        } else {
            $generateComplexType = $this->_generateDocumentComplexType($parameter, $methodName, $type);
            if ($generateComplexType) {
                return $generateComplexType;
            }
        }
        return null;
    }

    private function _generateDocumentSimpleType(MethodParser $method, $type)
    {
        $typeElement = new TypesElement();
        $typeElement->setName($method->getName() . $type . 'Parameters');

        if ($type == 'Request') {
            foreach ($method->parameters() as $parameter) {
                $parameterType = TypeHelper::getXsdType($parameter->getType());
                $typeElement->setElementAttributes('type', $parameterType, $parameter->getName());
            }
        } else {
            $returning = $method->returning();
            $returningType = TypeHelper::getXsdType($returning->getType());
            $typeElement->setElementAttributes('type', $returningType, $returning->getName());
        }
        return $typeElement;
    }

    private function _generateDocumentComplexType(Type $parameter, $methodName, $type)
    {
        if (TypeHelper::isArray($parameter)) {
            return $this->_generateDocumentArray($parameter, $methodName, $type);
        }
        if (TypeHelper::isObject($parameter)) {
            return $this->_generateObject($parameter, $methodName, $type);
        }
        return null;
    }

    private function _generateDocumentArray(Type $parameter, $methodName, $typeName)
    {
        $typesElement = new TypesElement();
        $typesElement->setName($methodName . $typeName . 'Parameters');

        $type = $parameter->getComplexType() ? 'ns:' : 'xsd:';

        $typesComplex = new TypesComplex();
        $typesComplex
            ->setName('ArrayOf' . ucfirst($parameter->getName()))
            ->setArrayType($type . $this->_getObjectName($parameter) . '[]');

        if ($parameter->getComplexType()) {
            $typesComplex->setComplex($this->_generateObject($parameter->getComplexType()));
        }

        $typesElement->setComplex($typesComplex);
        return $typesElement;
    }
}