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

/**
 * TypesComplex
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class TypesComplex
{
    private $_name;
    private $_arrayType;
    private $_arrayTypeName;
    private $_complex;

    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setArrayType($arrayType)
    {
        $this->_arrayType = $arrayType;
        return $this;
    }

    public function getArrayType()
    {
        return $this->_arrayType;
    }

    public function setComplex($complex)
    {
        $this->_complex = $complex;
        return $this;
    }

    public function getComplex()
    {
        return $this->_complex;
    }

    public function setArrayTypeName($arrayTypeName)
    {
        $this->_arrayTypeName = $arrayTypeName;
        return $this;
    }

    public function getArrayTypeName()
    {
        return $this->_arrayTypeName;
    }
}
