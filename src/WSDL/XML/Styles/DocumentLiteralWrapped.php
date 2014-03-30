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
            $elements[] = $this->_generateType($parameter, $method);
        }
        return $elements;
    }

    public function _generateType($parameter, MethodParser $method)
    {
        if (TypeHelper::isSimple($parameter)) {
            $generateComplexType = $this->_generateSimpleType($method, 'Request');
            if ($generateComplexType) {
                return $generateComplexType;
            }
        } else {
            $generateComplexType = $this->_generateComplexType($parameter);
            if ($generateComplexType) {
                return $generateComplexType;
            }
        }
        return null;
    }

    protected function _prepareTypeAndValue(Type $parameter)
    {
        $type = '';
        $value = '';
        if (TypeHelper::isSimple($parameter)) {
            $type = 'type';
            $value = TypeHelper::getXsdType($parameter->getType());
        } else if (TypeHelper::isArray($parameter)) {
            $type = 'type';
            $value = 'ns:' . 'ArrayOf' . ucfirst($parameter->getName());
        } else if (TypeHelper::isObject($parameter)) {
            $type = 'element';
            $value = 'ns:' . $this->_getObjectName($parameter);
        }
        return array($type, $value);
    }

    public function typeReturning(MethodParser $method)
    {

    }
}