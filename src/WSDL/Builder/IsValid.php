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
namespace WSDL\Builder;

use InvalidArgumentException;
use WSDL\Annotation\BindingType;
use WSDL\Annotation\SoapBinding;

/**
 * IsValid
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class IsValid
{
    /**
     * @param mixed $value
     * @param string|null $customMessage
     * @return void
     * @throws InvalidArgumentException
     */
    public static function notEmpty($value, $customMessage = null)
    {
        if (empty($value)) {
            $message = $customMessage ?: 'Value cannot be empty';
            throw new InvalidArgumentException($message);
        }
    }

    /**
     * @param string $style
     * @return void
     * @throws InvalidArgumentException
     */
    public static function style($style)
    {
        $styles = [SoapBinding::RPC, SoapBinding::DOCUMENT];
        if (!in_array($style, $styles)) {
            throw new InvalidArgumentException('Invalid style [' . $style . '] available styles: [' . implode(', ', $styles) . ']');
        }
    }

    /**
     * @param string $use
     * @return void
     * @throws InvalidArgumentException
     */
    public static function useStyle($use)
    {
        $uses = [SoapBinding::LITERAL, SoapBinding::ENCODED];
        if (!in_array($use, $uses)) {
            throw new InvalidArgumentException('Invalid use [' . $use . '] available uses: [' . implode(', ', $uses) . ']');
        }
    }

    /**
     * @param string $version
     * @return void
     * @throws InvalidArgumentException
     */
    public static function soapVersion($version)
    {
        $bindingTypes = [BindingType::SOAP_11, BindingType::SOAP_12];
        if (!in_array($version, $bindingTypes)) {
            throw new InvalidArgumentException('Invalid binding type [' . $version . '] available types: [' . implode(', ', $bindingTypes) . ']');
        }
    }
}
