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

use WSDL\Parser\MethodParser;

/**
 * RpcLiteral
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class RpcLiteral extends Style
{
    public function bindingStyle()
    {
        return 'rpc';
    }

    public function bindingUse()
    {
        return 'literal';
    }

    public function methodInput(MethodParser $method)
    {
        $partElements = array();
        foreach ($method->parameters() as $parameter) {
            $partElements[] = $this->_createElement($parameter);
        }
        return $partElements;
    }

    public function methodOutput(MethodParser $method)
    {
        $returnElement = $this->_createElement($method->returning());
        return $returnElement;
    }

    public function typeParameters(MethodParser $method)
    {
        $elements = array();
        foreach ($method->parameters() as $parameter) {
            $elements[] = $this->_generateType($parameter);
        }
        return $elements;
    }

    public function typeReturning(MethodParser $method)
    {
        return $this->_generateType($method->returning());
    }
}
