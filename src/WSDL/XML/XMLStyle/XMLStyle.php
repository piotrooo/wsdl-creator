<?php
/**
 * Copyright (C) 2013-2020
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

use DOMDocument;
use DOMElement;
use WSDL\Builder\Parameter;
use WSDL\Parser\Node;

/**
 * XMLStyle
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
interface XMLStyle
{
    public function generateBinding(DOMDocument $DOMDocument, string $soapVersion): DOMElement;

    /**
     * @param DOMDocument $DOMDocument
     * @param Node[] $nodes
     * @return DOMElement[]
     */
    public function generateMessagePart(DOMDocument $DOMDocument, array $nodes): array;

    /**
     * @param DOMDocument $DOMDocument
     * @param Parameter[] $parameters
     * @param string $soapVersion
     * @return DOMElement[]
     */
    public function generateTypes(DOMDocument $DOMDocument, array $parameters, string $soapVersion): array;
}
