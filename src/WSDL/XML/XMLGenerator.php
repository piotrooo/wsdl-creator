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
use WSDL\Types\Arrays;
use WSDL\Types\Object;
use WSDL\Types\Simple;
use WSDL\Types\Type;

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
    private $_alreadyGeneratedComplexTypes = array();

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
        $definitionsElement = $this->_createElementWithAttributes('definitions', array(
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

        $schemaElement = $this->_createElementWithAttributes('xsd:schema', array(
            'targetNamespace' => $this->_targetNamespaceTypes,
            'xmlns' => $this->_targetNamespaceTypes
        ));

        foreach ($this->_WSDLMethods as $method) {
            foreach ($method->parameters() as $parameter) {
                if (!$this->_isSimpleType($parameter)) {
                    $this->_generateComplexType($parameter, $schemaElement);
                }
            }
            $this->_generateComplexType($method->returning(), $schemaElement);
        }

        $typesElement->appendChild($schemaElement);
        $this->_definitionsRootNode->appendChild($typesElement);
        $this->_saveXML();
        return $this;
    }

    private function _generateComplexType(Type $parameter, $schemaElement)
    {
        if ($this->_isArrayType($parameter)) {
            $this->_generateArray($parameter, $schemaElement);
        }
        if ($this->_isObjectType($parameter)) {
            $this->_generateObject($parameter, $schemaElement);
        }
    }

    private function _generateArray(Type $parameter, $schemaElement)
    {
        $name = 'ArrayOf' . ucfirst($parameter->getName());
        if ($this->_isAlreadyGenerated($name)) {
            return;
        }

        $type = $parameter->getComplexType() ? 'ns:' : 'xsd:';

        $complexTypeElement = $this->_createElementWithAttributes('xsd:complexType', array('name' => $name));
        $complexContentElement = $this->_createElement('xsd:complexContent');
        $restrictionElement = $this->_createElementWithAttributes('xsd:restriction', array('base' => 'soapenc:Array'));
        $attributeElement = $this->_createElementWithAttributes('xsd:attribute', array(
            'ref' => 'soapenc:arrayType',
            'arrayType' => $type . $this->_getObjectName($parameter) . '[]'
        ));
        $restrictionElement->appendChild($attributeElement);
        $complexContentElement->appendChild($restrictionElement);
        $complexTypeElement->appendChild($complexContentElement);
        $schemaElement->appendChild($complexTypeElement);

        if ($parameter->getComplexType()) {
            $this->_generateObject($parameter->getComplexType(), $schemaElement);
        }
    }

    private function _generateObject(Type $parameter, $schemaElement)
    {
        $name = ucfirst($this->_getObjectName($parameter));
        if ($this->_isAlreadyGenerated($name)) {
            return;
        }

        $element = $this->_createElementWithAttributes('xsd:element', array(
            'name' => $name
        ));
        $complexTypeElement = $this->_createElement('xsd:complexType');
        $sequenceElement = $this->_createElement('xsd:sequence');

        $types = is_array($parameter->getComplexType()) ? $parameter->getComplexType() : $parameter->getComplexType()->getComplexType();
        foreach ($types as $complexType) {
            if ($complexType instanceof Type) {
                list($type, $value) = $this->_prepareTypeAndValue($complexType);
            } else {
                $type = 'type';
                $value = $this->_getXsdType($complexType->getType());
            }
            $elementPartElement = $this->_createElementWithAttributes('xsd:element', array(
                'name' => $complexType->getName(),
                $type => $value
            ));
            $sequenceElement->appendChild($elementPartElement);

            if ($this->_isArrayType($complexType)) {
                $this->_generateArray($complexType, $schemaElement);
            } else if ($complexType instanceof Type && !$this->_isSimpleType($complexType) && $complexType->getComplexType()) {
                $this->_generateComplexType($complexType->getComplexType(), $schemaElement);
            }
        }

        $complexTypeElement->appendChild($sequenceElement);
        $element->appendChild($complexTypeElement);
        $schemaElement->appendChild($element);
    }

    private function _isAlreadyGenerated($name)
    {
        if (in_array($name, $this->_alreadyGeneratedComplexTypes)) {
            return true;
        } else {
            $this->_alreadyGeneratedComplexTypes[] = $name;
            return false;
        }
    }

    /**
     * @return XMLGenerator
     */
    private function _message()
    {
        foreach ($this->_WSDLMethods as $method) {
            $messageInputElement = $this->_createElementWithAttributes('message', array(
                'name' => $method->getName() . 'Request'
            ));
            foreach ($this->_createXMLMessageInputParts($method) as $part) {
                $messageInputElement->appendChild($part);
            }
            $this->_definitionsRootNode->appendChild($messageInputElement);

            $messageOutputElement = $this->_createElementWithAttributes('message', array(
                'name' => $method->getName() . 'Response'
            ));
            $messageOutputElement->appendChild($this->_createXMLMessageOutput($method));
            $this->_definitionsRootNode->appendChild($messageOutputElement);
        }
        return $this;
    }

    private function _createXMLMessageInputParts(MethodParser $method)
    {
        $partElements = array();
        foreach ($method->parameters() as $parameter) {
            list($type, $value) = $this->_prepareTypeAndValue($parameter);

            $partElements[] = $this->_createElementWithAttributes('part', array(
                'name' => $parameter->getName(),
                $type => $value
            ));
        }
        return $partElements;
    }

    private function _createXMLMessageOutput(MethodParser $method)
    {
        $parameter = $method->returning();
        list($type, $value) = $this->_prepareTypeAndValue($parameter);

        $returnElement = $this->_createElementWithAttributes('part', array(
            'name' => $parameter->getName(),
            $type => $value
        ));
        return $returnElement;
    }

    private function _prepareTypeAndValue(Type $parameter)
    {
        if ($this->_isSimpleType($parameter)) {
            $type = 'type';
            $value = $this->_getXsdType($parameter->getType());
        } else if ($this->_isArrayType($parameter)) {
            $type = 'type';
            $value = 'ns:' . 'ArrayOf' . ucfirst($parameter->getName());
        } else if ($this->_isObjectType($parameter)) {
            $type = 'element';
            $value = 'ns:' . ucfirst($this->_getObjectName($parameter));
        }
        return array($type, $value);
    }

    private function _isSimpleType($type)
    {
        return $type instanceof Simple;
    }

    private function _isArrayType($type)
    {
        return $type instanceof Arrays;
    }

    private function _isObjectType($type)
    {
        return $type instanceof Object;
    }

    private function _getObjectName(Type $parameter)
    {
        return $parameter->getType() == 'object' ? ucfirst($parameter->getName()) : $parameter->getType();
    }

    /**
     * @return XMLGenerator
     */
    private function _portType()
    {
        $portTypeElement = $this->_createElementWithAttributes('portType', array(
            'name' => $this->_name . 'PortType'
        ));

        foreach ($this->_WSDLMethods as $method) {
            $operationElement = $this->_createElementWithAttributes('operation', array('name' => $method->getName()));

            if ($method->description()) {
                $documentationElement = $this->_createElement('documentation', $method->description());
                $operationElement->appendChild($documentationElement);
            }

            $inputElement = $this->_createElementWithAttributes('input', array('message' => 'tns:' . $method->getName() . 'Request'));
            $operationElement->appendChild($inputElement);

            $outputElement = $this->_createElementWithAttributes('output', array('message' => 'tns:' . $method->getName() . 'Response'));
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
        $bindingElement = $this->_createElementWithAttributes('binding', array(
            'name' => $this->_name . 'Binding',
            'type' => 'tns:' . $this->_name . 'PortType'
        ));

        $soapBindingElement = $this->_createElementWithAttributes('soap:binding', array(
            'style' => 'rpc',
            'transport' => 'http://schemas.xmlsoap.org/soap/http'
        ));
        $bindingElement->appendChild($soapBindingElement);

        foreach ($this->_WSDLMethods as $method) {
            $soapBodyElement = $this->_createElementWithAttributes('soap:body', array(
                'use' => 'literal'
            ));

            $operationElement = $this->_createElementWithAttributes('operation', array(
                'name' => $method->getName()
            ));

            $soapOperationElement = $this->_createElementWithAttributes('soap:operation', array(
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
        $serviceElement = $this->_createElementWithAttributes('service', array('name' => $this->_name . 'Service'));

        $portElement = $this->_createElementWithAttributes('port', array(
            'name' => $this->_name . 'Port',
            'binding' => 'tns:' . $this->_name . 'Binding'
        ));

        $soapAddressElement = $this->_createElementWithAttributes('soap:address', array('location' => $this->_location));
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

    private function _createElementWithAttributes($elementName, $attributes, $value = '')
    {
        $element = $this->_createElement($elementName, $value);
        foreach ($attributes as $attributeName => $attributeValue) {
            $tmpAttr = $this->_createAttributeWithValue($attributeName, $attributeValue);
            $element->appendChild($tmpAttr);
        }
        return $element;
    }

    private function _getXsdType($type)
    {
        return 'xsd:' . $type;
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