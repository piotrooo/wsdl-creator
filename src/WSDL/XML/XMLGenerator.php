<?php
/**
 * XMLWrapperGenerator
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 * @see http://www.xfront.com/GlobalVersusLocal.html
 */
namespace WSDL\XML;

use DOMDocument;
use WSDL\Parser\MethodParser;
use WSDL\WSDLObject\WSDLObject;

class XMLGenerator
{
    private $_name;
    private $_namespace;
    private $_targetNamespace;
    private $_targetNamespaceTypes;
    private $_xsd;
    private $_DOMDocument;
    private $_definitionsRootNode;
    private $_generatedXML;
    /**
     * @var WSDLObject
     */
    private $_WSDLObject;

    /**
     * @see http://infohost.nmt.edu/tcc/help/pubs/rnc/xsd.html
     */
    private $_parametersTypes = array(
        'string' => 'xsd:string',
        'integer' => 'xsd:int',
        'int' => 'xsd:int'
    );
    private $_location;

    public function __construct($name, $namespace, $location)
    {
        $this->_name = $name;
        $this->_namespace = $namespace;
        $this->_location = $location;

        $this->_targetNamespace = $this->_namespace . strtolower($name);
        $this->_xsd = 'http://www.w3.org/2001/XMLSchema';
        $this->_targetNamespaceTypes = $this->_targetNamespace . '/types';

        $this->_DOMDocument = new DOMDocument("1.0", "UTF-8");
        $this->_saveXML();
    }

    private function _saveXML()
    {
        $this->_generatedXML = $this->_DOMDocument->saveXML();
    }

    public function setWSDLObject(WSDLObject $WSDLObject)
    {
        $this->_WSDLObject = $WSDLObject;
        return $this;
    }

    public function generate()
    {
        $this->setDefinitions()->setTypes()->setMessage()->setPortType()->setBinding()->setService();
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

        $xmlnsXSDAttribute = $this->_createAttributeWithValue('xmlns:xsd', $this->_xsd);
        $definitionsElement->appendChild($xmlnsXSDAttribute);

        $xmlnsSoapAttribute = $this->_createAttributeWithValue('xmlns:soap', 'http://schemas.xmlsoap.org/wsdl/soap/');
        $definitionsElement->appendChild($xmlnsSoapAttribute);

        $xmlnsAttribute = $this->_createAttributeWithValue('xmlns', 'http://schemas.xmlsoap.org/wsdl/');
        $definitionsElement->appendChild($xmlnsAttribute);

        $xmlnsTypesAttribute = $this->_createAttributeWithValue('xmlns:ns', $this->_targetNamespaceTypes);
        $definitionsElement->appendChild($xmlnsTypesAttribute);

        $this->_DOMDocument->appendChild($definitionsElement);

        $this->_definitionsRootNode = $definitionsElement;

        $this->_saveXML();
        return $this;
    }

    public function setTypes()
    {
        $typesElement = $this->_createElement('types');

        $schemaElement = $this->_createElement('xsd:schema');
        $targetNamespaceAttribute = $this->_createAttributeWithValue('targetNamespace', $this->_targetNamespaceTypes);
        $schemaElement->appendChild($targetNamespaceAttribute);
        $xmlnsAttribute = $this->_createAttributeWithValue('xmlns', $this->_targetNamespaceTypes);
        $schemaElement->appendChild($xmlnsAttribute);

        $complexTypes = $this->_WSDLObject->getTypes();
        foreach ($complexTypes as $i => $complex) {
            $elementElement = $this->_createElement('xsd:element');
            $elementNameAttribute = $this->_createAttributeWithValue('name', $complex->getTypeName() . ($i + 1) . 'Input');
            $elementElement->appendChild($elementNameAttribute);

            foreach ($complex->getComplexTypes() as $type) {
                $complexTypeElement = $this->_createElement('xsd:complexType');
                $sequenceElement = $this->_createElement('xsd:sequence');
                foreach ($type as $complexSingle) {
                    $typeElement = $this->_createElement('xsd:element');

                    $typeNameAttribute = $this->_createAttributeWithValue('name', $complexSingle->getName());
                    $typeElement->appendChild($typeNameAttribute);


                    $typeTypeAttribute = $this->_createAttributeWithValue('type', $this->_getXsdType($complexSingle->getType()));
                    $typeElement->appendChild($typeTypeAttribute);

                    $sequenceElement->appendChild($typeElement);
                }
                $complexTypeElement->appendChild($sequenceElement);
            }

            $elementElement->appendChild($complexTypeElement);
            $schemaElement->appendChild($elementElement);

            $elementReturningElement = $this->_createElement('xsd:element');
            $elementReturningNameAttribute = $this->_createAttributeWithValue('name', $complex->getTypeName() . 'Output');
            $elementReturningElement->appendChild($elementReturningNameAttribute);

            $complexTypeReturningElement = $this->_createElement('xsd:complexType');
            $sequenceReturningElement = $this->_createElement('xsd:sequence');
            foreach ($complex->getReturningComplexType() as $returningComplexType) {
                $elementPartReturningElement = $this->_createElement('xsd:element');

                $typeNameReturningAttribute = $this->_createAttributeWithValue('name', $returningComplexType->getName());
                $elementPartReturningElement->appendChild($typeNameReturningAttribute);

                $typeTypeReturningAttribute = $this->_createAttributeWithValue('type', $this->_getXsdType($returningComplexType->getType()));
                $elementPartReturningElement->appendChild($typeTypeReturningAttribute);

                $sequenceReturningElement->appendChild($elementPartReturningElement);
            }
            $complexTypeReturningElement->appendChild($sequenceReturningElement);
            $elementReturningElement->appendChild($complexTypeReturningElement);
            $schemaElement->appendChild($elementReturningElement);
        }

        $typesElement->appendChild($schemaElement);

        $this->_definitionsRootNode->appendChild($typesElement);
        $this->_saveXML();
        return $this;
    }

    private function _getXsdType($type)
    {
        return isset($this->_parametersTypes[$type]) ? $this->_parametersTypes[$type] : 'xds:' . $type;
    }

    public function setMessage()
    {
        foreach ($this->_WSDLObject->getMethods() as $method) {
            $messageInputElement = $this->_createElement('message');
            $nameInput = $method->getName() . 'Request';
            $nameMessageInputAttribute = $this->_createAttributeWithValue('name', $nameInput);
            $messageInputElement->appendChild($nameMessageInputAttribute);

            $generatedParts = $this->_createXMLMessageInputParams($method);
            foreach ($generatedParts as $part) {
                $messageInputElement->appendChild($part);
            }

            $this->_definitionsRootNode->appendChild($messageInputElement);

            $messageOutputElement = $this->_createElement('message');
            $nameOutput = $method->getName() . 'Response';
            $nameMessageOutputAttribute = $this->_createAttributeWithValue('name', $nameOutput);
            $messageOutputElement->appendChild($nameMessageOutputAttribute);

            $generatedReturn = $this->_createXMLMessageOutput($method);
            $messageOutputElement->appendChild($generatedReturn);

            $this->_definitionsRootNode->appendChild($messageOutputElement);
        }
        return $this;
    }

    private function _createXMLMessageInputParams(MethodParser $method)
    {
        $XMLParam = array();
        foreach ($method->parameters() as $i => $param) {
            $XMLParam[$i] = $this->_createElement('part');

            $paramType = $param->getType();
            if ($param->isComplex()) {
                $paramNameAttribute = $this->_createAttributeWithValue('name', $param->getName());
                $XMLParam[$i]->appendChild($paramNameAttribute);

                $paramTypeAttribute = $this->_createAttributeWithValue('element', 'ns:' . $method->getName() . ($i + 1) . 'Input');
                $XMLParam[$i]->appendChild($paramTypeAttribute);
            } else {
                $paramNameAttribute = $this->_createAttributeWithValue('name', $param->getName());
                $XMLParam[$i]->appendChild($paramNameAttribute);

                $paramTypeAttribute = $this->_createAttributeWithValue('type', $this->_getXsdType($paramType));
                $XMLParam[$i]->appendChild($paramTypeAttribute);
            }
        }
        return $XMLParam;
    }

    private function _createXMLMessageOutput(MethodParser $method)
    {
        $parameter = $method->returning();

        $returnElement = $this->_createElement('part');

        $name = $parameter->getName() ? $parameter->getName() : $method->getName() . 'Output';
        $paramNameAttribute = $this->_createAttributeWithValue('name', $name);
        $returnElement->appendChild($paramNameAttribute);

        if ($parameter->isComplex()) {
            $paramTypeAttribute = $this->_createAttributeWithValue('element', 'ns:' . $method->getName() . 'Output');
            $returnElement->appendChild($paramTypeAttribute);
        } else {
            $paramTypeAttribute = $this->_createAttributeWithValue('type', $this->_getXsdType($parameter->getType()));
            $returnElement->appendChild($paramTypeAttribute);
        }
        return $returnElement;
    }

    public function setPortType()
    {
        $portTypeElement = $this->_createElement('portType');

        $name = $this->_name . 'PortType';
        $nameAttribute = $this->_createAttributeWithValue('name', $name);
        $portTypeElement->appendChild($nameAttribute);

        foreach ($this->_WSDLObject->getMethods() as $method) {
            $methodName = $method->getName();

            $operationElement = $this->_createElement('operation');
            $name = $this->_createAttributeWithValue('name', $methodName);
            $operationElement->appendChild($name);

            if ($method->description()) {
                $documentationElement = $this->_createElement('documentation', $method->description());
                $operationElement->appendChild($documentationElement);
            }

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
        $styleAttribute = $this->_createAttributeWithValue('style', 'rpc');
        $soapBindingElement->appendChild($styleAttribute);
        $transportAttribute = $this->_createAttributeWithValue('transport', 'http://schemas.xmlsoap.org/soap/http');
        $soapBindingElement->appendChild($transportAttribute);
        $bindingElement->appendChild($soapBindingElement);

        foreach ($this->_WSDLObject->getMethods() as $method) {
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
            $soapActionAttribute = $this->_createAttributeWithValue('soapAction', $this->_namespace . strtolower($this->_name) . '/#' . $methodName);
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

        $soapAddressElement = $this->_createElement('soap:address');
        $locationSoapAttribute = $this->_createAttributeWithValue('location', $this->_location);
        $soapAddressElement->appendChild($locationSoapAttribute);
        $portElement->appendChild($soapAddressElement);

        $serviceElement->appendChild($portElement);

        $this->_definitionsRootNode->appendChild($serviceElement);

        $this->_saveXML();
    }

    private function _createElement($elementName, $value = '')
    {
        return $this->_DOMDocument->createElement($elementName, $value);
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