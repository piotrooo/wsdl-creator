<?php
namespace WSDL\XML;

use DOMDocument;
use WSDL\Builder\WSDLBuilder;
use WSDL\Utilities\XMLAttributeHelper;

class XML
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

    public function generate()
    {
        $this->definitions()
            ->message()
            ->portType()
            ->binding()
            ->service();
    }

    public function render()
    {
        header("Content-Type: text/xml");
        echo $this->xml;
    }

    private function definitions()
    {
        $targetNamespace = $this->builder->getTargetNamespace();
        $definitionsElement = XMLAttributeHelper::forDOM($this->DOMDocument)
            ->createElementWithAttributes('definitions', array(
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
        $serviceElement = XMLAttributeHelper::forDOM($this->DOMDocument)
            ->createElementWithAttributes('service', array('name' => $name . 'Service'));

        $portElement = XMLAttributeHelper::forDOM($this->DOMDocument)
            ->createElementWithAttributes('port', array(
                'name' => $name . 'Port',
                'binding' => 'tns:' . $name . 'Binding'
            ));

        $soapAddressElement = XMLAttributeHelper::forDOM($this->DOMDocument)
            ->createElementWithAttributes('soap:address', array('location' => $this->builder->getLocation()));
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
        $bindingElement = XMLAttributeHelper::forDOM($this->DOMDocument)
            ->createElementWithAttributes('binding', array(
                'name' => $name . 'Binding',
                'type' => 'tns:' . $name . 'PortType'
            ));

        $soapBindingElement = $this->XMLStyle->getBindingDOMDocument($this->DOMDocument);
        $bindingElement->appendChild($soapBindingElement);

        foreach ($this->builder->getMethods() as $method) {
            $soapBodyElement = $this->XMLUse->getDOMDocument($this->DOMDocument, $targetNamespace);

            $operationElement = XMLAttributeHelper::forDOM($this->DOMDocument)
                ->createElementWithAttributes('operation', array(
                    'name' => $method->getName()
                ));

            $soapOperationElement = XMLAttributeHelper::forDOM($this->DOMDocument)
                ->createElementWithAttributes('soap:operation', array(
                    'soapAction' => $targetNamespace . '/#' . $method->getName()
                ));
            $operationElement->appendChild($soapOperationElement);

            $inputElement = XMLAttributeHelper::forDOM($this->DOMDocument)->createElement('input');
            $inputElement->appendChild($soapBodyElement);
            $operationElement->appendChild($inputElement);

            $outputElement = XMLAttributeHelper::forDOM($this->DOMDocument)->createElement('output');
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
        $portTypeElement = XMLAttributeHelper::forDOM($this->DOMDocument)
            ->createElementWithAttributes('portType', array(
                'name' => $name . 'PortType'
            ));

        foreach ($this->builder->getMethods() as $method) {
            $methodName = $method->getName();
            $operationElement = XMLAttributeHelper::forDOM($this->DOMDocument)
                ->createElementWithAttributes('operation', array('name' => $methodName));

            $inputElement = XMLAttributeHelper::forDOM($this->DOMDocument)
                ->createElementWithAttributes('input', array('message' => 'tns:' . $methodName . 'Request'));
            $operationElement->appendChild($inputElement);

            $outputElement = XMLAttributeHelper::forDOM($this->DOMDocument)
                ->createElementWithAttributes('output', array('message' => 'tns:' . $methodName . 'Response'));
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
        $messageInputElement = XMLAttributeHelper::forDOM($this->DOMDocument)
            ->createElementWithAttributes('message', array('name' => $name));
        $parts = $this->XMLStyle->getMessagePartDOMDocument($this->DOMDocument, $nodes);
        foreach ($parts as $part) {
            $messageInputElement->appendChild($part);
        }
        return $messageInputElement;
    }
}
