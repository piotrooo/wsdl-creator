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
use DOMElement;
use Ouzo\Utilities\Inflector;
use WSDL\Parser\Node;
use WSDL\Utilities\XMLAttributeHelper;

/**
 * XMLRpcStyle
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class XMLRpcStyle implements XMLStyle
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
            if ($node->isArray()) {
                $attributes = array(
                    'name' => $node->getSanitizedName(),
                    'type' => 'ns:' . $node->getNameForArray()
                );
            } else if ($node->isObject()) {
                $attributes = array(
                    'name' => $node->getSanitizedName(),
                    'element' => 'ns:' . $node->getNameForObject()
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

    /**
     * @inheritdoc
     */
    public function generateTypes(DOMDocument $DOMDocument, $parameters)
    {
        $return = array();
        foreach ($parameters as $parameter) {
            $node = $parameter->getNode();
            $nodeGen = $this->nodeGen($DOMDocument, $node);
            $return = array_merge($return, $nodeGen);
        }
        return $return;
    }

    private function nodeGen(DOMDocument $DOMDocument, Node $node, DOMElement $sequenceElement = null)
    {
        $res = array();
        if ($sequenceElement) {
            if ($node->isObject()) {
                $attributes = array(
                    'name' => $node->getNameForObject(),
                    'element' => 'ns:' . $node->getNameForObject()
                );
            } else if ($node->isArray()) {
                $attributes = array(
                    'name' => $node->getSanitizedName(),
                    'type' => 'ns:' . $node->getNameForArray()
                );
            } else {
                $attributes = array(
                    'name' => $node->getSanitizedName(),
                    'type' => 'xsd:' . $node->getType()
                );
            }
            $elementPartElement = XMLAttributeHelper::forDOM($DOMDocument)->createElementWithAttributes('xsd:element', $attributes);
            $sequenceElement->appendChild($elementPartElement);
        }
        if ($node->isArray()) {
            $complexTypeElement = XMLAttributeHelper::forDOM($DOMDocument)
                ->createElementWithAttributes('xsd:complexType', array('name' => $node->getNameForArray()));
            $complexContentElement = XMLAttributeHelper::forDOM($DOMDocument)->createElement('xsd:complexContent');
            $restrictionElement = XMLAttributeHelper::forDOM($DOMDocument)
                ->createElementWithAttributes('xsd:restriction', array('base' => 'soapenc:Array'));
            $type = $node->isObject() ? 'ns:' . $node->getNameForObject() . '[]' : 'xsd:' . $node->getType() . '[]';
            $attributeElement = XMLAttributeHelper::forDOM($DOMDocument)
                ->createElementWithAttributes('xsd:attribute', array(
                    'ref' => 'soapenc:arrayType',
                    'soap:arrayType' => $type
                ));
            $restrictionElement->appendChild($attributeElement);
            $complexContentElement->appendChild($restrictionElement);
            $complexTypeElement->appendChild($complexContentElement);
            $res[] = $complexTypeElement;
        }
        if ($node->isObject()) {
            $name = $node->getNameForObject();
            $element = XMLAttributeHelper::forDOM($DOMDocument)->createElementWithAttributes('xsd:element', array(
                'name' => $name, 'nillable' => 'true', 'type' => 'ns:' . $name
            ));
            $res[] = $element;

            $complexTypeElement = XMLAttributeHelper::forDOM($DOMDocument)
                ->createElementWithAttributes('xsd:complexType', array('name' => $node->getNameForObject()));
            $sequenceElement = XMLAttributeHelper::forDOM($DOMDocument)->createElement('xsd:sequence');
            $complexTypeElement->appendChild($sequenceElement);

            $res[] = $complexTypeElement;
            foreach ($node->getElements() as $nodeElement) {
                $tmp = $this->nodeGen($DOMDocument, $nodeElement, $sequenceElement);
                $res = array_merge($res, $tmp);
            }
        }
        return $res;
    }
}
