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
namespace WSDL\XML\XMLStyle;

use DOMDocument;
use WSDL\Utilities\XMLAttributeHelper;

/**
 * XMLRpcStyleStrategy
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class XMLRpcStyleStrategy implements XMLStyleStrategy
{
    /**
     * @inheritdoc
     */
    public function generateBinding(DOMDocument $DOMDocument)
    {
        return XMLAttributeHelper::forDOM($DOMDocument)
            ->createElementWithAttributes('soap:binding', array(
                'style' => 'rpc',
                'transport' => 'http://schemas.xmlsoap.org/soap/http'
            ));
    }

    /**
     * @inheritdoc
     */
    public function generateMessagePart(DOMDocument $DOMDocument, $nodes)
    {
        $parts = array();
        foreach ($nodes as $node) {
            if ($node->isObject()) {
                $attributes = array(
                    'name' => $node->getSanitizedName(),
                    'type' => 'ns:' . $node->getTypeForObject()
                );
            } else if ($node->isArray()) {
                $attributes = array(
                    'name' => $node->getSanitizedName(),
                    'type' => 'ns:' . $node->getTypeForArray()
                );
            } else {
                $attributes = array(
                    'name' => $node->getSanitizedName(),
                    'type' => 'xsd:' . $node->getType()
                );
            }
            $parts[] = XMLAttributeHelper::forDOM($DOMDocument)->createElementWithAttributes('part', $attributes);
        }
        return $parts;
    }
}