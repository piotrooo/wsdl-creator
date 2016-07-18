<?php
namespace WSDL\XML;

use DOMDocument;
use WSDL\Builder\WSDLBuilder;
use WSDL\Utilities\XMLAttributeHelper;
use WSDL\XML\XMLStyle\XMLStyle;
use WSDL\XML\XMLUse\XMLUse;

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
     * @var DOMDocument
     */
    private $DOMDocument;
    /**
     * @var string
     */
    private $xml;
    /**
     * @var DOMDocument
     */
    private $definitionsRootNode;

    public function __construct(WSDLBuilder $builder)
    {
        $this->builder = $builder;
        $this->XMLStyle = new XMLStyle($builder->getStyle());
        $this->XMLUse = new XMLUse($builder->getUse());
        $this->DOMDocument = new DOMDocument("1.0", "UTF-8");
        $this->DOMDocument->formatOutput = true;
        $this->saveXML();
    }

    private function saveXML()
    {
        $this->xml = $this->DOMDocument->saveXML();
    }

    public function getXml()
    {
        return $this->xml;
    }

    public function generate()
    {
        $this->definitions()
            ->types()
            ->message()
            ->portType()
            ->binding()
            ->service();
    }

    private function definitions()
    {
        $targetNamespace = $this->builder->getTargetNamespace();
        $definitionsElement = $this->createElementWithAttributes('definitions', array(
            'name' => $this->builder->getName(),
            'targetNamespace' => $targetNamespace,
            'xmlns:tns' => $targetNamespace,
            'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
            'xmlns:soap' => 'http://schemas.xmlsoap.org/wsdl/soap/',
            'xmlns:soapenc' => "http://schemas.xmlsoap.org/soap/encoding/",
            'xmlns' => 'http://schemas.xmlsoap.org/wsdl/',
            'xmlns:ns' => $this->builder->getNs()
        ));
        $this->DOMDocument->appendChild($definitionsElement);
        $this->definitionsRootNode = $definitionsElement;
        $this->saveXML();
        return $this;
    }

    private function service()
    {
        $name = $this->builder->getName();
        $serviceElement = $this->createElementWithAttributes('service', array('name' => $name . 'Service'));

        $portElement = $this->createElementWithAttributes('port', array('name' => $name . 'Port', 'binding' => 'tns:' . $name . 'Binding'));

        $soapAddressElement = $this->createElementWithAttributes('soap:address', array('location' => $this->builder->getLocation()));
        $portElement->appendChild($soapAddressElement);

        $serviceElement->appendChild($portElement);
        $this->definitionsRootNode->appendChild($serviceElement);
        $this->saveXML();
        return $this;
    }

    private function binding()
    {
        $name = $this->builder->getName();
        $targetNamespace = $this->builder->getTargetNamespace();
        $bindingElement = $this->createElementWithAttributes('binding', array('name' => $name . 'Binding', 'type' => 'tns:' . $name . 'PortType'));

        $soapBindingElement = $this->XMLStyle->getBindingDOMDocument($this->DOMDocument);
        $bindingElement->appendChild($soapBindingElement);

        foreach ($this->builder->getMethods() as $method) {
            $soapBodyElement = $this->XMLUse->getBodyDOMDocument($this->DOMDocument, $targetNamespace);

            $operationElement = $this->createElementWithAttributes('operation', array('name' => $method->getName()));

            $soapOperationElement = $this->createElementWithAttributes('soap:operation', array(
                'soapAction' => $targetNamespace . '/#' . $method->getName()
            ));
            $operationElement->appendChild($soapOperationElement);

            $inputElement = $this->createElement('input');
            $inputElement->appendChild($soapBodyElement);
            $operationElement->appendChild($inputElement);

            $outputElement = $this->createElement('output');
            $outputElement->appendChild($soapBodyElement->cloneNode());
            $operationElement->appendChild($outputElement);

            $bindingElement->appendChild($operationElement);
        }

        $this->definitionsRootNode->appendChild($bindingElement);
        $this->saveXML();
        return $this;
    }

    private function portType()
    {
        $name = $this->builder->getName();
        $portTypeElement = $this->createElementWithAttributes('portType', array('name' => $name . 'PortType'));

        foreach ($this->builder->getMethods() as $method) {
            $methodName = $method->getName();
            $operationElement = $this->createElementWithAttributes('operation', array('name' => $methodName));

            $inputElement = $this->createElementWithAttributes('input', array('message' => 'tns:' . $methodName . 'Request'));
            $operationElement->appendChild($inputElement);

            $outputElement = $this->createElementWithAttributes('output', array('message' => 'tns:' . $methodName . 'Response'));
            $operationElement->appendChild($outputElement);

            $portTypeElement->appendChild($operationElement);
        }

        $this->definitionsRootNode->appendChild($portTypeElement);
        $this->saveXML();
        return $this;
    }

    private function message()
    {
        foreach ($this->builder->getMethods() as $method) {
            $name = $method->getName();

            $messageInputElement = $this->messageParts($name . 'Request', $method->getParameters());
            $this->definitionsRootNode->appendChild($messageInputElement);

            $messageOutputElement = $this->messageParts($name . 'Response', $method->getReturn());
            $this->definitionsRootNode->appendChild($messageOutputElement);
        }
        return $this;
    }

    private function messageParts($name, $nodes)
    {
        $messageElement = $this->createElementWithAttributes('message', array('name' => $name));
        $parts = $this->XMLStyle->getMessagePartDOMDocument($this->DOMDocument, $nodes);
        foreach ($parts as $part) {
            $messageElement->appendChild($part);
        }
        return $messageElement;
    }

    private function types()
    {
        $ns = $this->builder->getNs();
        $typesElement = XMLAttributeHelper::forDOM($this->DOMDocument)->createElement('types');

        $schemaElement = XMLAttributeHelper::forDOM($this->DOMDocument)
            ->createElementWithAttributes('xsd:schema', array('targetNamespace' => $ns, 'xmlns' => $ns));
        $typesElement->appendChild($schemaElement);

        $this->definitionsRootNode->appendChild($typesElement);
        $this->saveXML();
        return $this;
    }

    private function createElementWithAttributes($elementName, $attributes, $value = '')
    {
        return XMLAttributeHelper::forDOM($this->DOMDocument)->createElementWithAttributes($elementName, $attributes, $value);
    }

    private function createElement($elementName, $value = '')
    {
        return XMLAttributeHelper::forDOM($this->DOMDocument)->createElement($elementName, $value);
    }
}
