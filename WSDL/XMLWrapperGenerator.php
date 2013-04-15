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
    private $_targetNamespace;
    private $_DOMDocument;
    private $_definitionsRootNode;
    private $_generatedXML;

    public function __construct($name, $targetNamespace)
    {
        $this->_name = $name;
        $this->_targetNamespace = $targetNamespace;
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

        $xmlnsXSDAttribute = $this->_createAttributeWithValue('xmlns:xsd1', $this->_targetNamespace);
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

        $soapBodyElement = $this->_createElement('soap:body');
        $use = $this->_createAttributeWithValue('use', 'literal');
        $soapBodyElement->appendChild($use);

        foreach ($methods as $method) {
            $operationElement = $this->_createElement('operation');
            $name = $this->_createAttributeWithValue('name', $method);
            $operationElement->appendChild($name);

            $inputElement = $this->_createElement('input');
            $inputElement->appendChild($soapBodyElement);
            $operationElement->appendChild($inputElement);

            $outputElement = $this->_createElement('output');
            $outputElement->appendChild($soapBodyElement);
            $operationElement->appendChild($outputElement);

            $bindingElement->appendChild($operationElement);
        }

        $this->_definitionsRootNode->appendChild($bindingElement);

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