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
namespace WSDL\XML;

use DOMAttr;
use DOMDocument;
use DOMElement;

/**
 * XMLAttributeHelper
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class XMLAttributeHelper
{
    /**
     * @var DOMDocument
     */
    private $DOMDocument;

    public function __construct(DOMDocument $DOMDocument)
    {
        $this->DOMDocument = $DOMDocument;
    }

    public function createElementWithAttributes(string $elementName, array $attributes, string $value = ''): DOMElement
    {
        $element = $this->createElement($elementName, $value);
        foreach ($attributes as $attributeName => $attributeValue) {
            $tmpAttr = $this->createAttributeWithValue($attributeName, $attributeValue);
            $element->appendChild($tmpAttr);
        }

        return $element;
    }

    public function createAttributeWithValue(string $attributeName, string $value): DOMAttr
    {
        $attribute = $this->DOMDocument->createAttribute($attributeName);
        $attribute->value = $value;

        return $attribute;
    }

    public function createElement(string $elementName, string $value = ''): DOMElement
    {
        return $this->DOMDocument->createElement($elementName, $value);
    }

    public static function forDOM(DOMDocument $DOMDocument): XMLAttributeHelper
    {
        return new self($DOMDocument);
    }
}
