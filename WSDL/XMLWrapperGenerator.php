<?php
/**
 * XMLWrapperGenerator
 *
 * @author Piotr Olaszewski
 */
namespace WSDL;

class XMLWrapperGenerator
{
    private $_name;
    private $_namespace;
    private $_targetNamespace;
    private $_xsd1;
    private $_DOMDocument;
    private $_definitionsRootNode;
    private $_generatedXML;

    public function __construct($name, $namespace)
    {
        $this->_name = $name;
        $this->_namespace = $namespace;

        $this->_targetNamespace = $this->_namespace . strtolower($this->_name) . '.wsdl';
        $this->_xsd1 = $this->_namespace . strtolower($this->_name) . '.xsd';

        $this->_DOMDocument = new \DOMDocument("1.0", "UTF-8");

        $this->_saveXML();
    }

    private function _saveXML()
    {
        $this->_generatedXML = $this->_DOMDocument->saveXML();
    }

    public function setDefinitions()
    {
        $definitionsElement = $this->_createElement('definitions');

        $name = $this->_createAttributeWithValue('name', $this->_name);
        $definitionsElement->appendChild($name);

        $targetNamespaceAttribute = $this->_createAttributeWithValue('targetNamespace', $this->_targetNamespace);
        $definitionsElement->appendChild($targetNamespaceAttribute);

        $xmlnsTnsAttribute = $this->_createAttributeWithValue('xmlns:tns', $this->_targetNamespace);
        $definitionsElement->appendChild($xmlnsTnsAttribute);

        $xmlnsXSDAttribute = $this->_createAttributeWithValue('xmlns:xsd1', $this->_xsd1);
        $definitionsElement->appendChild($xmlnsXSDAttribute);

        $xmlnsSoapAttribute = $this->_createAttributeWithValue('xmlns:soap', 'http://schemas.xmlsoap.org/wsdl/soap/');
        $definitionsElement->appendChild($xmlnsSoapAttribute);

        $xmlnsAttribute = $this->_createAttributeWithValue('xmlns', 'http://schemas.xmlsoap.org/wsdl/');
        $definitionsElement->appendChild($xmlnsAttribute);

        $this->_DOMDocument->appendChild($definitionsElement);

        $this->_definitionsRootNode = $definitionsElement;

        $this->_saveXML();

        return $this;
    }

    public function setMessage($methods, $parsedComments)
    {
        foreach ($methods as $method) {
            $messageInputElement = $this->_createElement('message');
            $nameInput = $method . 'Request';
            $nameMessageInputAttribute = $this->_createAttributeWithValue('name', $nameInput);
            $messageInputElement->appendChild($nameMessageInputAttribute);

            $this->_definitionsRootNode->appendChild($messageInputElement);

            $messageOutputElement = $this->_createElement('message');
            $nameOutput = $method . 'Response';
            $nameMessageOutputAttribute = $this->_createAttributeWithValue('name', $nameOutput);
            $messageOutputElement->appendChild($nameMessageOutputAttribute);

            $this->_definitionsRootNode->appendChild($messageOutputElement);
        }

        return $this;
    }

    public function setPortType($methods)
    {
        $portTypeElement = $this->_createElement('portType');

        $name = $this->_name . 'PortType';
        $nameAttribute = $this->_createAttributeWithValue('name', $name);
        $portTypeElement->appendChild($nameAttribute);

        foreach ($methods as $method) {
            $operationElement = $this->_createElement('operation');
            $name = $this->_createAttributeWithValue('name', $method);
            $operationElement->appendChild($name);

            $inputElement = $this->_createElement('input');
            $methodInputMessage = $method . 'Request';
            $messageInputAttribute = $this->_createAttributeWithValue('message', 'tns:' . $methodInputMessage);
            $inputElement->appendChild($messageInputAttribute);
            $operationElement->appendChild($inputElement);

            $outputElement = $this->_createElement('output');
            $methodOutputMessage = $method . 'Response';
            $messageOutputAttribute = $this->_createAttributeWithValue('message', 'tns:' . $methodOutputMessage);
            $outputElement->appendChild($messageOutputAttribute);
            $operationElement->appendChild($outputElement);

            $portTypeElement->appendChild($operationElement);
        }

        $this->_definitionsRootNode->appendChild($portTypeElement);

        return $this;
    }

    public function setBinding($methods)
    {
        $bindingElement = $this->_createElement('binding');

        $name = $this->_name . 'Binding';
        $nameAttribute = $this->_createAttributeWithValue('name', $name);
        $bindingElement->appendChild($nameAttribute);

        $type = $this->_name . 'PortType';
        $typeAttribute = $this->_createAttributeWithValue('type', $type);
        $bindingElement->appendChild($typeAttribute);

        $soapBindingElement = $this->_createElement('soap:binding');
        $styleAttribute = $this->_createAttributeWithValue('style', 'document');
        $soapBindingElement->appendChild($styleAttribute);
        $transportAttribute = $this->_createAttributeWithValue('transport', 'http://schemas.xmlsoap.org/soap/http');
        $soapBindingElement->appendChild($transportAttribute);
        $bindingElement->appendChild($soapBindingElement);

        $soapBodyElementInput = $this->_createElement('soap:body');
        $use = $this->_createAttributeWithValue('use', 'literal');
        $soapBodyElementInput->appendChild($use);

        $soapBodyElementOutput = $this->_createElement('soap:body');
        $use = $this->_createAttributeWithValue('use', 'literal');
        $soapBodyElementOutput->appendChild($use);

        foreach ($methods as $method) {
            $operationElement = $this->_createElement('operation');
            $name = $this->_createAttributeWithValue('name', $method);
            $operationElement->appendChild($name);

            $soapOperationElement = $this->_createElement('soap:operation');
            $soapActionAttribute = $this->_createAttributeWithValue('soapAction', $this->_namespace . $method);
            $soapOperationElement->appendChild($soapActionAttribute);
            $operationElement->appendChild($soapOperationElement);

            $inputElement = $this->_createElement('input');
            $inputElement->appendChild($soapBodyElementInput);
            $operationElement->appendChild($inputElement);

            $outputElement = $this->_createElement('output');
            $outputElement->appendChild($soapBodyElementOutput);
            $operationElement->appendChild($outputElement);

            $bindingElement->appendChild($operationElement);
        }

        $this->_definitionsRootNode->appendChild($bindingElement);

        $this->_saveXML();

        return $this;
    }

    public function setService()
    {
        $serviceElement = $this->_createElement('service');

        $name = $this->_name . 'Service';
        $nameAttribute = $this->_createAttributeWithValue('name', $name);
        $serviceElement->appendChild($nameAttribute);

        $portElement = $this->_createElement('port');
        $namePortAttribute = $this->_createAttributeWithValue('name', $this->_name . 'Port');
        $portElement->appendChild($namePortAttribute);
        $bindingAttribute = $this->_createAttributeWithValue('binding', 'tns:' . $this->_name . 'Binding');
        $portElement->appendChild($bindingAttribute);

        $serviceElement->appendChild($portElement);

        $this->_definitionsRootNode->appendChild($serviceElement);

        $this->_saveXML();
    }

    private function _createElement($elementName)
    {
        return $this->_DOMDocument->createElement($elementName);
    }

    private function _createAttributeWithValue($attributeName, $value)
    {
        $attribute = $this->_DOMDocument->createAttribute($attributeName);
        $attribute->value = $value;

        return $attribute;
    }

    public function render()
    {
        echo $this->_generatedXML;
    }
}