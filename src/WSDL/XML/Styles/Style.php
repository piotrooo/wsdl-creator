<?php
/**
 * Copyright (C) 2013-2016
 * Piotr Olaszewski <piotroo89@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace WSDL\XML\Styles;

use Ouzo\Utilities\Inflector;
use WSDL\Parser\MethodParser;
use WSDL\Types\Type;
use WSDL\Utilities\Strings;
use WSDL\Utilities\TypeHelper;

/**
 * Style
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
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
        } elseif (TypeHelper::isArray($parameter)) {
            $type = 'type';
            $value = 'ns:' . 'ArrayOf' . ucfirst($parameter->getName());
        } elseif (TypeHelper::isObject($parameter)) {
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
            ->setArrayTypeName(Inflector::singularize($parameter->getName()));

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
        } elseif ($complexType instanceof Type && !TypeHelper::isSimple($complexType) && $complexType->getComplexType()) {
            $typesElement->setComplex($this->_generateComplexType($complexType->getComplexType()));
        }
    }
}
