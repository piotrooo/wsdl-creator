<?php
/**
 * Style
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

use WSDL\Parser\MethodParser;
use WSDL\Types\Type;
use WSDL\Utilities\TypeHelper;

abstract class Style
{
    abstract public function bindingStyle();

    abstract public function bindingUse();

    abstract public function methodInput(MethodParser $method);

    abstract public function methodOutput(MethodParser $method);

    abstract public function typeParameters(MethodParser $method);

    abstract public function typeReturning(MethodParser $method);

    protected function _createElement(Type $returning)
    {
        list($type, $value) = $this->_prepareTypeAndValue($returning);
        $element = array(
            'name' => $returning->getName(),
            $type => $value
        );
        return $element;
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

    protected function _getObjectName(Type $parameter)
    {
        return $parameter->getType() == 'object' ? ucfirst($parameter->getName()) : $parameter->getType();
    }

    protected function _generateType($parameter)
    {
        if (!TypeHelper::isSimple($parameter)) {
            $generateComplexType = $this->_generateComplexType($parameter);
            if ($generateComplexType) {
                return $generateComplexType;
            }
        }
        return null;
    }

    protected function _generateComplexType(Type $parameter)
    {
        if (TypeHelper::isArray($parameter)) {
            return $this->_generateArray($parameter);
        }
        if (TypeHelper::isObject($parameter)) {
            return $this->_generateObject($parameter);
        }
        return null;
    }

    protected function _generateArray(Type $parameter)
    {
        $type = $parameter->getComplexType() ? 'ns:' : 'xsd:';

        $typesComplex = new TypesComplex();
        $typesComplex
            ->setName('ArrayOf' . ucfirst($parameter->getName()))
            ->setArrayType($type . $this->_getObjectName($parameter) . '[]')
            ->setArrayTypeName(\WSDL\Utilities\Strings::depluralize($parameter->getName()));

        if ($parameter->getComplexType()) {
            $typesComplex->setComplex($this->_generateObject($parameter->getComplexType()));
        }

        return $typesComplex;
    }

    protected function _generateObject(Type $parameter)
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

            $this->_setComplexTypeIfNeeded($complexType, $typesElement);
        }
        return $typesElement;
    }

    protected function _setComplexTypeIfNeeded($complexType, TypesElement $typesElement)
    {
        if (TypeHelper::isArray($complexType)) {
            $typesElement->setComplex($this->_generateArray($complexType));
        } else if ($complexType instanceof Type && !TypeHelper::isSimple($complexType) && $complexType->getComplexType()) {
            $typesElement->setComplex($this->_generateComplexType($complexType->getComplexType()));
        }
    }
}
