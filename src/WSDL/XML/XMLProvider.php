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
namespace WSDL\XML;

use DOMDocument;
use DOMElement;
use Ouzo\Utilities\Arrays;
use WSDL\Builder\Parameter;
use WSDL\Builder\WSDLBuilder;
use WSDL\Parser\Node;
use WSDL\XML\XMLSoapVersion\XMLSoapVersion;
use WSDL\XML\XMLStyle\XMLStyle;
use WSDL\XML\XMLStyle\XMLStyleFactory;
use WSDL\XML\XMLUse\XMLUse;
use WSDL\XML\XMLUse\XMLUseFactory;

/**
 * XMLProvider
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class XMLProvider
{
    /**
     * @var WSDLBuilder
     */
    private $builder;
    /**
     * @var XMLStyle
     */
    private $XMLStyle;
    /**
     * @var XMLUse
     */
    private $XMLUse;
    /**
     * @var string
     */
    private $XMLSoapVersion;
    /**
     * @var DOMDocument
     */
    private $DOMDocument;
    /**
     * @var string
     */
    private $xml;
    /**
     * @var DOMElement
     */
    private $definitionsRootNode;

    /**
     * @param WSDLBuilder $builder
     */
    public function __construct(WSDLBuilder $builder)
    {
        $this->builder = $builder;
        $this->XMLStyle = XMLStyleFactory::create($builder->getStyle());
        $this->XMLUse = XMLUseFactory::create($builder->getUse());
        $this->XMLSoapVersion = XMLSoapVersion::getTagFor($builder->getSoapVersion());
        $this->DOMDocument = new DOMDocument("1.0", "UTF-8");
        $this->DOMDocument->formatOutput = true;
    }

    /**
     * @return string
     */
    public function getXml()
    {
        $this->xml = $this->DOMDocument->saveXML();
        return $this->xml;
    }

    /**
     * @return void
     */
    public function generate()
    {
        $this->definitions()
            ->types()
            ->message()
            ->portType()
            ->binding()
            ->service();
    }

    /**
     * @return $this
     */
    private function definitions()
    {
        $targetNamespace = $this->builder->getTargetNamespace();
        $definitionsElement = $this->createElementWithAttributes('definitions', [
            'name' => $this->builder->getName(),
            'targetNamespace' => $targetNamespace,
            'xmlns:tns' => $targetNamespace,
            'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
            'xmlns:' . $this->XMLSoapVersion => 'http://schemas.xmlsoap.org/wsdl/' . $this->XMLSoapVersion . '/',
            'xmlns:soapenc' => "http://schemas.xmlsoap.org/soap/encoding/",
            'xmlns' => 'http://schemas.xmlsoap.org/wsdl/',
            'xmlns:ns' => $this->builder->getNs()
        ]);
        $this->DOMDocument->appendChild($definitionsElement);
        $this->definitionsRootNode = $definitionsElement;
        return $this;
    }

    /**
     * @return $this
     */
    private function service()
    {
        $name = $this->builder->getName();
        $serviceElement = $this->createElementWithAttributes('service', ['name' => $name . 'Service']);

        $portElement = $this->createElementWithAttributes('port', ['name' => $name . 'Port', 'binding' => 'tns:' . $name . 'Binding']);

        $soapAddressElement = $this
            ->createElementWithAttributes($this->XMLSoapVersion . ':address', ['location' => $this->builder->getLocation()]);
        $portElement->appendChild($soapAddressElement);

        $serviceElement->appendChild($portElement);
        $this->definitionsRootNode->appendChild($serviceElement);
        return $this;
    }

    /**
     * @return $this
     */
    private function binding()
    {
        $name = $this->builder->getName();
        $targetNamespace = $this->builder->getTargetNamespace();
        $bindingElement = $this->createElementWithAttributes('binding', ['name' => $name . 'Binding', 'type' => 'tns:' . $name . 'PortType']);

        $soapBindingElement = $this->XMLStyle->generateBinding($this->DOMDocument, $this->XMLSoapVersion);
        $bindingElement->appendChild($soapBindingElement);

        foreach ($this->builder->getMethods() as $method) {
            $methodName = $method->getName();
            $operationElement = $this->createElementWithAttributes('operation', ['name' => $methodName]);
            $soapOperationElement = $this->createElementWithAttributes($this->XMLSoapVersion . ':operation', [
                'soapAction' => $targetNamespace . '/#' . $methodName
            ]);
            $operationElement->appendChild($soapOperationElement);

            $soapBodyElement = $this->XMLUse->generateSoapBody($this->DOMDocument, $targetNamespace, $this->XMLSoapVersion);
            $this->bindingElement($methodName, $soapBodyElement, $operationElement, 'input', 'RequestHeader', $method->getHeaderParameter());
            $this->bindingElement($methodName, $soapBodyElement, $operationElement, 'output', 'ResponseHeader', $method->getHeaderReturn());

            $bindingElement->appendChild($operationElement);
        }

        $this->definitionsRootNode->appendChild($bindingElement);
        return $this;
    }

    /**
     * @param string $methodName
     * @param DOMElement $soapBodyElement
     * @param DOMElement $element
     * @param string $elementName
     * @param string $headerName
     * @param Parameter|null $header
     */
    private function bindingElement($methodName, DOMElement $soapBodyElement, DOMElement $element, $elementName, $headerName, Parameter $header = null)
    {
        $targetNamespace = $this->builder->getTargetNamespace();
        $inputElement = $this->createElement($elementName);
        $inputElement->appendChild($soapBodyElement->cloneNode());

        $soapHeaderMessage = 'tns:' . $methodName . $headerName;
        $soapHeaderElement = $this->XMLUse
            ->generateSoapHeaderIfNeeded($this->DOMDocument, $targetNamespace, $soapHeaderMessage, $header, $this->XMLSoapVersion);
        if ($soapHeaderElement) {
            $inputElement->appendChild($soapHeaderElement);
        }

        $element->appendChild($inputElement);
    }

    /**
     * @return $this
     */
    private function portType()
    {
        $name = $this->builder->getName();
        $portTypeElement = $this->createElementWithAttributes('portType', ['name' => $name . 'PortType']);

        foreach ($this->builder->getMethods() as $method) {
            $methodName = $method->getName();
            $operationElement = $this->createElementWithAttributes('operation', ['name' => $methodName]);

            $inputElement = $this->createElementWithAttributes('input', ['message' => 'tns:' . $methodName . 'Request']);
            $operationElement->appendChild($inputElement);

            $outputElement = $this->createElementWithAttributes('output', ['message' => 'tns:' . $methodName . 'Response']);
            $operationElement->appendChild($outputElement);

            $portTypeElement->appendChild($operationElement);
        }

        $this->definitionsRootNode->appendChild($portTypeElement);
        return $this;
    }

    /**
     * @return $this
     */
    private function message()
    {
        foreach ($this->builder->getMethods() as $method) {
            $name = $method->getName();

            $this->messageHeaderIfNeeded($name, 'RequestHeader', $method->getHeaderParameter());
            $messageInputElement = $this->messageParts($name . 'Request', $method->getNoHeaderParametersNodes());
            $this->definitionsRootNode->appendChild($messageInputElement);

            $this->messageHeaderIfNeeded($name, 'ResponseHeader', $method->getHeaderReturn());
            $messageOutputElement = $this->messageParts($name . 'Response', $method->getReturnNode());
            $this->definitionsRootNode->appendChild($messageOutputElement);
        }
        return $this;
    }

    /**
     * @param string $methodName
     * @param string $headerSuffix
     * @param Parameter|null $parameter
     */
    private function messageHeaderIfNeeded($methodName, $headerSuffix, Parameter $parameter = null)
    {
        if ($parameter) {
            $messageHeaderElement = $this->messageParts($methodName . $headerSuffix, $parameter->getNode());
            $this->definitionsRootNode->appendChild($messageHeaderElement);
        }
    }

    /**
     * @param string $methodName
     * @param Node|Node[] $nodes
     * @return DOMElement
     */
    private function messageParts($methodName, $nodes)
    {
        $messageElement = $this->createElementWithAttributes('message', ['name' => $methodName]);
        if ($nodes !== null) {
            $nodes = Arrays::toArray($nodes);
            $parts = $this->XMLStyle->generateMessagePart($this->DOMDocument, $nodes);
            foreach ($parts as $part) {
                $messageElement->appendChild($part);
            }
        }
        return $messageElement;
    }

    /**
     * @return $this
     */
    private function types()
    {
        $ns = $this->builder->getNs();
        $typesElement = $this->createElement('types');

        $schemaElement = $this->createElementWithAttributes('xsd:schema', ['targetNamespace' => $ns, 'xmlns' => $ns]);
        foreach ($this->builder->getMethods() as $method) {
            $typesForParameters = $this->XMLStyle->generateTypes($this->DOMDocument, $method->getParameters(), $this->XMLSoapVersion);
            $typesForReturn = $this->XMLStyle->generateTypes($this->DOMDocument, Arrays::toArray($method->getReturn()), $this->XMLSoapVersion);
            $types = array_merge($typesForParameters, $typesForReturn);
            foreach ($types as $type) {
                $schemaElement->appendChild($type);
            }
        }
        $typesElement->appendChild($schemaElement);

        $this->definitionsRootNode->appendChild($typesElement);
        return $this;
    }

    /**
     * @param string $elementName
     * @param array $attributes
     * @param string $value
     * @return DOMElement
     */
    private function createElementWithAttributes($elementName, $attributes, $value = '')
    {
        return XMLAttributeHelper::forDOM($this->DOMDocument)->createElementWithAttributes($elementName, $attributes, $value);
    }

    /**
     * @param string $elementName
     * @param string $value
     * @return DOMElement
     */
    private function createElement($elementName, $value = '')
    {
        return XMLAttributeHelper::forDOM($this->DOMDocument)->createElement($elementName, $value);
    }
}
