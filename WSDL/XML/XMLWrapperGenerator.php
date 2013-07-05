<?php
/**
 * XMLWrapperGenerator
 *
 * @author Piotr Olaszewski
 * @see http://www.xfront.com/GlobalVersusLocal.html
 */
namespace WSDL;

use DOMDocument;
use WSDL\Parser\MethodParser;

class XMLWrapperGenerator
{
    private $_name;
    private $_namespace;
    private $_targetNamespace;
    private $_xsd1;
    private $_DOMDocument;
    private $_definitionsRootNode;
    private $_generatedXML;
    /**
     * @var MethodParser[]
     */
    private $_methods;
    private $_parsedClass;

    /**
     * @see http://infohost.nmt.edu/tcc/help/pubs/rnc/xsd.html
     */
    private $_parametersTypes = array(
        'string' => 'xsd:string',
        'integer' => 'xsd:int',
        'int' => 'xsd:int'
    );

    public function __construct($name, $namespace)
    {
        $this->_name = $name;
        $this->_namespace = $namespace;

        $this->_targetNamespace = $this->_namespace . strtolower($this->_name) . '.wsdl';
        $this->_xsd1 = $this->_namespace . strtolower($this->_name) . '.xsd';

        $this->_DOMDocument = new DOMDocument("1.0", "UTF-8");
        $this->_saveXML();
    }

    private function _saveXML()
    {
        $this->_generatedXML = $this->_DOMDocument->saveXML();
    }

    public function setMethods($methods)
    {
        $this->_methods = $methods;
        return $this;
    }

    public function setParsedClass($parsedClass)
    {
        $this->_parsedClass = $parsedClass;
        return $this;
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

        $xmlnsSoapAttribute = $this->_createAttributeWithValue('xmlns:soap', 'http://schemas.xmlsoap.org/WSDL/soap/');
        $definitionsElement->appendChild($xmlnsSoapAttribute);

        $xmlnsAttribute = $this->_createAttributeWithValue('xmlns', 'http://schemas.xmlsoap.org/WSDL/');
        $definitionsElement->appendChild($xmlnsAttribute);

        $this->_DOMDocument->appendChild($definitionsElement);

        $this->_definitionsRootNode = $definitionsElement;

        $this->_saveXML();

        return $this;
    }

    public function setTypes()
    {
        return $this;
    }

    public function setMessage()
    {
        foreach ($this->_methods as $method) {
            $messageInputElement = $this->_createElement('message');
            $nameInput = $method->getName() . 'Request';
            $nameMessageInputAttribute = $this->_createAttributeWithValue('name', $nameInput);
            $messageInputElement->appendChild($nameMessageInputAttribute);

            $generatedParts = $this->_createXMLMessageParams($method->parameters());
            foreach ($generatedParts as $part) {
                $messageInputElement->appendChild($part);
            }

            $this->_definitionsRootNode->appendChild($messageInputElement);

            $messageOutputElement = $this->_createElement('message');
            $nameOutput = $method->getName() . 'Response';
            $nameMessageOutputAttribute = $this->_createAttributeWithValue('name', $nameOutput);
            $messageOutputElement->appendChild($nameMessageOutputAttribute);

            $generatedReturn = $this->_createXMLMessageReturn($nameOutput, $method->returning());
            $messageOutputElement->appendChild($generatedReturn);

            $this->_definitionsRootNode->appendChild($messageOutputElement);
        }
        return $this;
    }

    private function _createXMLMessageParams($params)
    {
        $XMLparam = array();
        foreach ($params as $i => $param) {
            $paramType = array_keys($param);
            $paramName = array_values($param);
            $name = is_array(current($param)) ? $i : $paramName[0];

            $XMLparam[$i] = $this->_createElement('part');

            $paramNameAttribute = $this->_createAttributeWithValue('name', $name);
            $XMLparam[$i]->appendChild($paramNameAttribute);

            $paramTypeAttribute = $this->_createAttributeWithValue('type', $this->_parametersTypes[$paramType[0]]);
            $XMLparam[$i]->appendChild($paramTypeAttribute);
        }
        return $XMLparam;
    }

    private function _createXMLMessageReturn($method, $return)
    {
        $returnElement = $this->_createElement('part');

        $paramNameAttribute = $this->_createAttributeWithValue('name', $method);
        $returnElement->appendChild($paramNameAttribute);

        $paramTypeAttribute = $this->_createAttributeWithValue('type', $this->_parametersTypes[$return]);
        $returnElement->appendChild($paramTypeAttribute);
        return $returnElement;
    }

    public function setPortType()
    {
        $portTypeElement = $this->_createElement('portType');

        $name = $this->_name . 'PortType';
        $nameAttribute = $this->_createAttributeWithValue('name', $name);
        $portTypeElement->appendChild($nameAttribute);

        foreach ($this->_methods as $method) {
            $methodName = $method->getName();

            $operationElement = $this->_createElement('operation');
            $name = $this->_createAttributeWithValue('name', $methodName);
            $operationElement->appendChild($name);

            $inputElement = $this->_createElement('input');
            $methodInputMessage = $methodName . 'Request';
            $messageInputAttribute = $this->_createAttributeWithValue('message', 'tns:' . $methodInputMessage);
            $inputElement->appendChild($messageInputAttribute);
            $operationElement->appendChild($inputElement);

            $outputElement = $this->_createElement('output');
            $methodOutputMessage = $methodName . 'Response';
            $messageOutputAttribute = $this->_createAttributeWithValue('message', 'tns:' . $methodOutputMessage);
            $outputElement->appendChild($messageOutputAttribute);
            $operationElement->appendChild($outputElement);

            $portTypeElement->appendChild($operationElement);
        }
        $this->_definitionsRootNode->appendChild($portTypeElement);
        return $this;
    }

    public function setBinding()
    {
        $bindingElement = $this->_createElement('binding');

        $name = $this->_name . 'Binding';
        $nameAttribute = $this->_createAttributeWithValue('name', $name);
        $bindingElement->appendChild($nameAttribute);

        $type = 'tns:' . $this->_name . 'PortType';
        $typeAttribute = $this->_createAttributeWithValue('type', $type);
        $bindingElement->appendChild($typeAttribute);

        $soapBindingElement = $this->_createElement('soap:binding');
        $styleAttribute = $this->_createAttributeWithValue('style', 'document');
        $soapBindingElement->appendChild($styleAttribute);
        $transportAttribute = $this->_createAttributeWithValue('transport', 'http://schemas.xmlsoap.org/soap/http');
        $soapBindingElement->appendChild($transportAttribute);
        $bindingElement->appendChild($soapBindingElement);

        foreach ($this->_methods as $method) {
            $methodName = $method->getName();

            $soapBodyElementInput = $this->_createElement('soap:body');
            $use = $this->_createAttributeWithValue('use', 'literal');
            $soapBodyElementInput->appendChild($use);

            $soapBodyElementOutput = $this->_createElement('soap:body');
            $use = $this->_createAttributeWithValue('use', 'literal');
            $soapBodyElementOutput->appendChild($use);

            $operationElement = $this->_createElement('operation');
            $name = $this->_createAttributeWithValue('name', $methodName);
            $operationElement->appendChild($name);

            $soapOperationElement = $this->_createElement('soap:operation');
            $soapActionAttribute = $this->_createAttributeWithValue('soapAction', $this->_namespace . $methodName);
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