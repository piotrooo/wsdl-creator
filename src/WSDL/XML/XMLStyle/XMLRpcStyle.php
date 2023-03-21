<?php
/**
 * Copyright (C) 2013-2022
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
use WSDL\Parser\Node;
use WSDL\XML\XMLAttributeHelper;

/**
 * XMLRpcStyle
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class XMLRpcStyle implements XMLStyle
{
    public function generateBinding(DOMDocument $DOMDocument, string $soapVersion): DOMElement
    {
        return XMLAttributeHelper::forDOM($DOMDocument)
            ->createElementWithAttributes($soapVersion . ':binding', [
                'style' => 'rpc', 'transport' => 'http://schemas.xmlsoap.org/soap/http'
            ]);
    }

    /**
     * @inheritdoc
     */
    public function generateMessagePart(DOMDocument $DOMDocument, array $nodes): array
    {
        $parts = [];
        foreach ($nodes as $node) {
            $attributes = $this->attributes($node);
            $parts[] = XMLAttributeHelper::forDOM($DOMDocument)->createElementWithAttributes('part', $attributes);
        }

        return $parts;
    }

    /**
     * @inheritdoc
     */
    public function generateTypes(DOMDocument $DOMDocument, array $parameters, string $soapVersion): array
    {
        $types = [];
        foreach ($parameters as $parameter) {
            $node = $parameter->getNode();
            $nodeGen = $this->generateParameters($DOMDocument, $node, null, $soapVersion);
            $types = array_merge($types, $nodeGen);
        }

        return $types;
    }

    /**
     * @param DOMDocument $DOMDocument
     * @param Node $node
     * @param DOMElement|null $sequenceElement
     * @param string $soapVersion
     * @return DOMElement[]
     */
    private function generateParameters(DOMDocument $DOMDocument, Node $node, DOMElement $sequenceElement = null, string $soapVersion): array
    {
        $result = [];
        if ($sequenceElement) {
            $attributes = $this->attributes($node);
            $elementPartElement = XMLAttributeHelper::forDOM($DOMDocument)->createElementWithAttributes('xsd:element', $attributes);
            $sequenceElement->appendChild($elementPartElement);
        }
        if ($node->isArray()) {
            $complexTypeElement = XMLAttributeHelper::forDOM($DOMDocument)
                ->createElementWithAttributes('xsd:complexType', ['name' => $node->getNameForArray()]);
            $complexContentElement = XMLAttributeHelper::forDOM($DOMDocument)->createElement('xsd:complexContent');
            $restrictionElement = XMLAttributeHelper::forDOM($DOMDocument)
                ->createElementWithAttributes('xsd:restriction', ['base' => 'soapenc:Array']);
            $type = $node->isObject() ? 'ns:' . $node->getNameForObject() . '[]' : 'xsd:' . $node->getType() . '[]';
            $attributeElement = XMLAttributeHelper::forDOM($DOMDocument)
                ->createElementWithAttributes('xsd:attribute', [
                    'ref' => 'soapenc:arrayType',
                    $soapVersion . ':arrayType' => $type
                ]);
            $restrictionElement->appendChild($attributeElement);
            $complexContentElement->appendChild($restrictionElement);
            $complexTypeElement->appendChild($complexContentElement);
            $result[] = $complexTypeElement;
        }
        if ($node->isObject()) {
            $name = $node->getNameForObject();
            $element = XMLAttributeHelper::forDOM($DOMDocument)->createElementWithAttributes('xsd:element', [
                'name' => $name, 'nillable' => 'true', 'type' => 'ns:' . $name
            ]);
            $result[] = $element;

            $complexTypeElement = XMLAttributeHelper::forDOM($DOMDocument)
                ->createElementWithAttributes('xsd:complexType', ['name' => $node->getNameForObject()]);
            $sequenceElement = XMLAttributeHelper::forDOM($DOMDocument)->createElement('xsd:sequence');
            $complexTypeElement->appendChild($sequenceElement);

            $result[] = $complexTypeElement;
            foreach ($node->getElements() as $nodeElement) {
                $tmp = $this->generateParameters($DOMDocument, $nodeElement, $sequenceElement, $soapVersion);
                $result = array_merge($result, $tmp);
            }
        }

        return $result;
    }

    private function attributes(Node $node): array
    {
        if ($node->isArray()) {
            $attributes = ['name' => $node->getSanitizedName(), 'type' => 'ns:' . $node->getNameForArray()];
        } elseif ($node->isObject()) {
            $attributes = ['name' => $node->getSanitizedName(), 'element' => 'ns:' . $node->getNameForObject()];
        } else {
            $attributes = ['name' => $node->getSanitizedName(), 'type' => 'xsd:' . $node->getType()];
        }

        return $attributes;
    }
}
