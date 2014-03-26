<?php
/**
 * RpcLiteral
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

use WSDL\Types\Type;
use WSDL\Utilities\TypeHelper;

class RpcLiteral implements Style
{
    public function bindingStyle()
    {
        return 'rpc';
    }

    public function bindingUse()
    {
        return 'literal';
    }

    /**
     * @param Type[] $parameters
     * @return array
     */
    public function methodInput($parameters)
    {
        $partElements = array();
        foreach ($parameters as $parameter) {
            $partElements[] = $this->_createElement($parameter);
        }
        return $partElements;
    }

    /**
     * @param Type $returning
     * @return array
     */
    public function methodOutput($returning)
    {
        $returnElement = $this->_createElement($returning);
        return $returnElement;
    }

    /**
     * @param Type[] $parameters
     * @return array
     */
    public function typeParameters($parameters)
    {
        $elements = array();
        foreach ($parameters as $parameter) {
            if (!TypeHelper::isSimple($parameter)) {
                $generateComplexType = $this->_generateComplexType($parameter);
                if ($generateComplexType) {
                    $elements[] = $generateComplexType;
                }
            }
        }
        return $elements;
    }

    private function _generateComplexType(Type $parameter)
    {
        if (TypeHelper::isArray($parameter)) {
            return $this->_generateArray($parameter);
        }
        if (TypeHelper::isObject($parameter)) {
            return $this->_generateObject($parameter);
        }
        return null;
    }

    private function _generateArray(Type $parameter)
    {
        $type = $parameter->getComplexType() ? 'ns:' : 'xsd:';

        $typesComplex = new TypesComplex();
        $typesComplex
            ->setName('ArrayOf' . ucfirst($parameter->getName()))
            ->setArrayType($type . $this->_getObjectName($parameter) . '[]');

        if ($parameter->getComplexType()) {
            $typesComplex->setComplex($this->_generateObject($parameter->getComplexType()));
        }

        return $typesComplex;
    }

    private function _generateObject(Type $parameter)
    {
        $typesElement = new TypesElement();
        $typesElement->setName($this->_getObjectName($parameter));

        $types = is_array($parameter->getComplexType()) ? $parameter->getComplexType() : $parameter->getComplexType()->getComplexType();

        foreach ($types as $complexType) {
            if ($complexType instanceof Type) {
                list($type, $value) = $this->_prepareTypeAndValue($complexType);
            } else {
                $type = 'type';
                $value = TypeHelper::getXsdType($complexType->getType());
            }

            $typesElement->setElementAttributes($type, $value, $complexType->getName());

            if (TypeHelper::isArray($complexType)) {
                $typesElement->setComplex($this->_generateArray($complexType));
            } else if ($complexType instanceof Type && !TypeHelper::isSimple($complexType) && $complexType->getComplexType()) {
                $typesElement->setComplex($this->_generateComplexType($complexType->getComplexType()));
            }
        }
        return $typesElement;
    }

    private function _createElement(Type $returning)
    {
        list($type, $value) = $this->_prepareTypeAndValue($returning);
        $element = array(
            'name' => $returning->getName(),
            $type => $value
        );
        return $element;
    }

    private function _prepareTypeAndValue(Type $parameter)
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

    private function _getObjectName(Type $parameter)
    {
        return $parameter->getType() == 'object' ? ucfirst($parameter->getName()) : $parameter->getType();
    }
}