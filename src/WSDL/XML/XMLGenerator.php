<?php
/**
 * XMLGenerator
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 * @see http://www.xfront.com/GlobalVersusLocal.html
 */
namespace WSDL\XML;

use DOMDocument;
use ReflectionClass;
use WSDL\Parser\MethodParser;
use WSDL\XML\Styles\Style;
use WSDL\XML\Styles\TypesComplex;
use WSDL\XML\Styles\TypesElement;

class XMLGenerator
{
    private $_name;
    private $_location;
    private $_targetNamespace;
    private $_targetNamespaceTypes;
    /**
     * @var DOMDocument
     */
    private $_DOMDocument;
    /**
     * @var DOMDocument
     */
    private $_definitionsRootNode;
    /**
     * @var DOMDocument
     */
    private $_generatedXML;
    /**
     * @var MethodParser[]
     */
    private $_WSDLMethods;
    /**
     * @var Style
     */
    private $_bindingStyle;

    public static $alreadyGeneratedComplexTypes = array();

    public function __construct($name, $namespace, $location)
    {
        $sanitizedName = $this->sanitizeClassName($name);
        $this->_name = $this->extractClassName($name);
        $this->_location = $location;

        $this->_targetNamespace = $namespace . strtolower($sanitizedName);
        $this->_targetNamespaceTypes = $this->_targetNamespace . '/types';

        $this->_DOMDocument = new DOMDocument("1.0", "UTF-8");
        $this->_saveXML();
    }

    public function sanitizeClassName($name)
    {
        return ltrim(str_replace('\\', '/', $name), '/');
    }

    public function extractClassName($name)
    {
        $reflectedClass = new ReflectionClass($name);
        return $reflectedClass->getShortName();
    }

    public function setWSDLMethods($WSDLMethods)
    {
        $this->_WSDLMethods = $WSDLMethods;
        return $this;
    }

    public function setBindingStyle(Style $_bindingStyle)
    {
        $this->_bindingStyle = $_bindingStyle;
        return $this;
    }

    public function generate()
    {
        $this->_definitions()
            ->_types()
            ->_message()->_portType()->_binding()->_service();
    }

    /**
     * @return XMLGenerator
     */
    private function _definitions()
    {
        $definitionsElement = $this->createElementWithAttributes('definitions', array(
            'name' => $this->_name,
            'targetNamespace' => $this->_targetNamespace,
            'xmlns:tns' => $this->_targetNamespace,
            'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
            'xmlns:soap' => 'http://schemas.xmlsoap.org/wsdl/soap/',
            'xmlns:soapenc' => "http://schemas.xmlsoap.org/soap/encoding/",
            'xmlns' => 'http://schemas.xmlsoap.org/wsdl/',
            'xmlns:ns' => $this->_targetNamespaceTypes
        ));
        $this->_DOMDocument->appendChild($definitionsElement);
        $this->_definitionsRootNode = $definitionsElement;
        $this->_saveXML();
        return $this;
    }

    /**
     * @return XMLGenerator
     */
    private function _types()
    {
        $typesElement = $this->_createElement('types');

        $schemaElement = $this->createElementWithAttributes('xsd:schema', array(
            'targetNamespace' => $this->_targetNamespaceTypes,
            'xmlns' => $this->_targetNamespaceTypes
        ));

        foreach ($this->_WSDLMethods as $method) {
            $typeParameters = $this->_bindingStyle->typeParameters($method);
            if ($typeParameters) {
                $this->_typesParameters($typeParameters, $schemaElement);
            }

            $typeReturning = $this->_bindingStyle->typeReturning($method);
            $this->_generateComplexType($typeReturning, $schemaElement);
        }

        $typesElement->appendChild($schemaElement);
        $this->_definitionsRootNode->appendChild($typesElement);
        $this->_saveXML();
        return $this;
    }

    private function _typesParameters($parameters, $schemaElement)
    {
        foreach ($parameters as $parameter) {
            $this->_generateComplexType($parameter, $schemaElement);
        }
    }

    private function _generateComplexType($parameter, $schemaElement)
    {
        if ($parameter instanceof TypesComplex) {
            $this->_generateArray($parameter, $schemaElement);
        }
        if ($parameter instanceof TypesElement) {
            $this->_generateObject($parameter, $schemaElement);
        }
    }

    private function _generateArray(TypesComplex $parameter, $schemaElement)
    {
        $name = $parameter->getName();
        $type = $parameter->getArrayType();

        if (self::isAlreadyGenerated($name)) {
            return;
        }

        $complexTypeElement = $this->createElementWithAttributes('xsd:complexType', array('name' => $name));
        $complexContentElement = $this->_createElement('xsd:complexContent');
        $restrictionElement = $this->createElementWithAttributes('xsd:restriction', array('base' => 'soapenc:Array'));
        $attributeElement = $this->createElementWithAttributes('xsd:attribute', array(
            'ref' => 'soapenc:arrayType',
            'arrayType' => $type
        ));
        $restrictionElement->appendChild($attributeElement);
        $complexContentElement->appendChild($restrictionElement);
        $complexTypeElement->appendChild($complexContentElement);
        $schemaElement->appendChild($complexTypeElement);

        if ($parameter->getComplex()) {
            $this->_generateComplexType($parameter->getComplex(), $schemaElement);
        }
    }

    private function _generateObject(TypesElement $parameter, $schemaElement)
    {
        $name = $parameter->getName();

        if (self::isAlreadyGenerated($name)) {
            return;
        }

        $element = $this->createElementWithAttributes('xsd:element', array(
            'name' => $name
        ));
        $complexTypeElement = $this->_createElement('xsd:complexType');
        $sequenceElement = $this->_createElement('xsd:sequence');

        $types = $parameter->getElementAttributes();
        foreach ($types as $complexType) {
            $elementPartElement = $this->createElementWithAttributes('xsd:element', array(
                'name' => $complexType['name'],
                $complexType['type'] => $complexType['value']
            ));
            $sequenceElement->appendChild($elementPartElement);
        }

        if ($parameter->getComplex()) {
            $this->_generateComplexType($parameter->getComplex(), $schemaElement);
        }

        $complexTypeElement->appendChild($sequenceElement);
        $element->appendChild($complexTypeElement);
        $schemaElement->appendChild($element);
    }

    public static function isAlreadyGenerated($name)
    {
        if (in_array($name, self::$alreadyGeneratedComplexTypes)) {
            return true;
        } else {
            self::$alreadyGeneratedComplexTypes[] = $name;
            return false;
        }
    }

    /**
     * @return XMLGenerator
     */
    private function _message()
    {
        foreach ($this->_WSDLMethods as $method) {
            $messageInputElement = $this->_messageInput($method);
            $this->_definitionsRootNode->appendChild($messageInputElement);

            $messageOutputElement = $this->_messageOutput($method);
            $this->_definitionsRootNode->appendChild($messageOutputElement);
        }
        return $this;
    }

    private function _messageInput(MethodParser $method)
    {
        $messageInputElement = $this->createElementWithAttributes('message', array(
            'name' => $method->getName() . 'Request'
        ));
        $partsInput = $this->_bindingStyle->methodInput($method);
        $obj = $this;
        $partsInput = array_map(function ($attributes) use ($obj) {
            return $obj->createElementWithAttributes('part', $attributes);
        }, $partsInput);
        foreach ($partsInput as $part) {
            $messageInputElement->appendChild($part);
        }
        return $messageInputElement;
    }

    private function _messageOutput(MethodParser $method)
    {
        $messageOutputElement = $this->createElementWithAttributes('message', array(
            'name' => $method->getName() . 'Response'
        ));
        $partsOutput = $this->_bindingStyle->methodOutput($method);
        $partsOutput = $this->createElementWithAttributes('part', $partsOutput);
        $messageOutputElement->appendChild($partsOutput);
        return $messageOutputElement;
    }

    /**
     * @return XMLGenerator
     */
    private function _portType()
    {
        $portTypeElement = $this->createElementWithAttributes('portType', array(
            'name' => $this->_name . 'PortType'
        ));

        foreach ($this->_WSDLMethods as $method) {
            $operationElement = $this->createElementWithAttributes('operation', array('name' => $method->getName()));

            if ($method->description()) {
                $documentationElement = $this->_createElement('documentation', $method->description());
                $operationElement->appendChild($documentationElement);
            }

            $inputElement = $this->createElementWithAttributes('input', array('message' => 'tns:' . $method->getName() . 'Request'));
            $operationElement->appendChild($inputElement);

            $outputElement = $this->createElementWithAttributes('output', array('message' => 'tns:' . $method->getName() . 'Response'));
            $operationElement->appendChild($outputElement);

            $portTypeElement->appendChild($operationElement);
        }
        $this->_definitionsRootNode->appendChild($portTypeElement);
        $this->_saveXML();
        return $this;
    }

    /**
     * @return XMLGenerator
     */
    private function _binding()
    {
        $bindingElement = $this->createElementWithAttributes('binding', array(
            'name' => $this->_name . 'Binding',
            'type' => 'tns:' . $this->_name . 'PortType'
        ));

        $soapBindingElement = $this->createElementWithAttributes('soap:binding', array(
            'style' => $this->_bindingStyle->bindingStyle(),
            'transport' => 'http://schemas.xmlsoap.org/soap/http'
        ));
        $bindingElement->appendChild($soapBindingElement);

        foreach ($this->_WSDLMethods as $method) {
            $soapBodyElement = $this->createElementWithAttributes('soap:body', array(
                'use' => $this->_bindingStyle->bindingUse(),
                'namespace' => $this->_targetNamespace
            ));

            $operationElement = $this->createElementWithAttributes('operation', array(
                'name' => $method->getName()
            ));

            $soapOperationElement = $this->createElementWithAttributes('soap:operation', array(
                'soapAction' => $this->_targetNamespace . '/#' . $method->getName()
            ));
            $operationElement->appendChild($soapOperationElement);

            $inputElement = $this->_createElement('input');
            $inputElement->appendChild($soapBodyElement);
            $operationElement->appendChild($inputElement);

            $outputElement = $this->_createElement('output');
            $outputElement->appendChild($soapBodyElement->cloneNode());
            $operationElement->appendChild($outputElement);

            $bindingElement->appendChild($operationElement);
        }
        $this->_definitionsRootNode->appendChild($bindingElement);
        $this->_saveXML();
        return $this;
    }

    /**
     * @return XMLGenerator
     */
    private function _service()
    {
        $serviceElement = $this->createElementWithAttributes('service', array('name' => $this->_name . 'Service'));

        $portElement = $this->createElementWithAttributes('port', array(
            'name' => $this->_name . 'Port',
            'binding' => 'tns:' . $this->_name . 'Binding'
        ));

        $soapAddressElement = $this->createElementWithAttributes('soap:address', array('location' => $this->_location));
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

    public function createElementWithAttributes($elementName, $attributes, $value = '')
    {
        $element = $this->_createElement($elementName, $value);
        foreach ($attributes as $attributeName => $attributeValue) {
            $tmpAttr = $this->_createAttributeWithValue($attributeName, $attributeValue);
            $element->appendChild($tmpAttr);
        }
        return $element;
    }

    private function _saveXML()
    {
        $this->_generatedXML = $this->_DOMDocument->saveXML();
    }

    public function getGeneratedXML()
    {
        return $this->_generatedXML;
    }

    public function render()
    {
        echo $this->_generatedXML;
    }
}
