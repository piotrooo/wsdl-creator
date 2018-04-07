<?php
/**
 * Copyright (C) 2013-2018
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
namespace WSDL\XML\XMLStyle;

use Exception;
use WSDL\Annotation\SoapBinding;
use WSDL\XML\XMLParameterStyle\XMLParameterStyleFactory;

/**
 * XMLStyleFactory
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class XMLStyleFactory
{
    public static function create(string $style, string $parameterStyle = SoapBinding::BARE): XMLStyle
    {
        $XMLParameterStyle = XMLParameterStyleFactory::create($parameterStyle);
        switch ($style) {
            case SoapBinding::RPC:
                return new XMLRpcStyle($XMLParameterStyle);
            case SoapBinding::DOCUMENT:
                return new XMLDocumentStyle();
            default:
                throw new Exception('Unsupported soap binding style [' . $style . ']');
        }
    }
}
