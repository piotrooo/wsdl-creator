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

    public function __construct(WSDLBuilder $builder)
    {
        $this->builder = $builder;
        $this->XMLStyle = XMLStyleFactory::create($builder->getStyle(), $builder->getParameterStyle());
        $this->XMLUse = XMLUseFactory::create($builder->getUse());
        $this->XMLSoapVersion = XMLSoapVersion::getTagFor($builder->getSoapVersion());
        $this->DOMDocument = new DOMDocument("1.0", "UTF-8");
        $this->DOMDocument->formatOutput = true;
    }

    public function getXml(): string
    {
        $this->xml = $this->DOMDocument->saveXML();

        return $this->xml;
    }

    public function generate(): void
    {
        $this->definitions()
            ->types()
            ->message()
            ->portType()
            ->binding()
            ->service();
    }

    private function definitions(): XMLProvider
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

    private function service(): XMLProvider
    {
        $name = $this->builder->getName();
        $serviceElement = $this->createElementWithAttributes('service', ['name' => $name . 'Service']);

        $portName = $this->builder->getPortName();
        $portElement = $this->createElementWithAttributes('port', ['name' => $portName . 'Port', 'binding' => 'tns:' . $name . 'Binding']);

        $soapAddressElement = $this
            ->createElementWithAttributes($this->XMLSoapVersion . ':address', ['location' => $this->builder->getLocation()]);
        $portElement->appendChild($soapAddressElement);

        $serviceElement->appendChild($portElement);
        $this->definitionsRootNode->appendChild($serviceElement);

        return $this;
    }

    private function binding(): XMLProvider
    {
        $name = $this->builder->getName();
        $targetNamespace = $this->builder->getTargetNamespace();
        $bindingElement = $this->createElementWithAttributes('binding', ['name' => $name . 'Binding', 'type' => 'tns:' . $name . 'Port']);

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

    private function bindingElement(string $methodName, DOMElement $soapBodyElement, DOMElement $element, string $elementName, string $headerName, Parameter $header = null): void
    {
        $targetNamespace = $this->builder->getTargetNamespace();
        $inputElement = $this->createElement($elementName);
        $inputElement->appendChild($soapBodyElement->cloneNode());

        $soapHeaderMessage = 'tns:' . $methodName . $headerName;
        $soapHeaderElement = $this->XMLUse
            ->generateSoapHeaderIfNeeded($this->DOMDocument, $targetNamespace, $this->XMLSoapVersion, $soapHeaderMessage, $header);
        if ($soapHeaderElement) {
            $inputElement->appendChild($soapHeaderElement);
        }

        $element->appendChild($inputElement);
    }

    private function portType(): XMLProvider
    {
        $name = $this->builder->getName();
        $portTypeElement = $this->createElementWithAttributes('portType', ['name' => $name . 'Port']);

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

    private function message(): XMLProvider
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

    private function messageHeaderIfNeeded(string $methodName, string $headerSuffix, Parameter $parameter = null): void
    {
        if ($parameter) {
            $messageHeaderElement = $this->messageParts($methodName . $headerSuffix, $parameter->getNode());
            $this->definitionsRootNode->appendChild($messageHeaderElement);
        }
    }

    /**
     * @param string $methodName
     * @param Node|Node[]|null $nodes
     * @return DOMElement
     */
    private function messageParts(string $methodName, $nodes): DOMElement
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

    private function types(): XMLProvider
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

    private function createElementWithAttributes(string $elementName, array $attributes, string $value = ''): DOMElement
    {
        return XMLAttributeHelper::forDOM($this->DOMDocument)->createElementWithAttributes($elementName, $attributes, $value);
    }

    private function createElement(string $elementName, string $value = ''): DOMElement
    {
        return XMLAttributeHelper::forDOM($this->DOMDocument)->createElement($elementName, $value);
    }
}
