<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Xml;

use DOMDocument;
use ReflectionClass;
use WsdlCreator\Annotation\SOAPBindingStyle;
use WsdlCreator\Internal\Model\Service;
use WsdlCreator\Xml\Message\XmlGeneratorDocumentStrategyFactory;
use WsdlCreator\Xml\Type\XmlGeneratorTypeStrategyFactory;

/**
 * Generates XML for WSDL specification.
 *
 * @author Piotr Olaszewski
 */
class XmlGenerator
{
    private XmlClassModeler $xmlClassModeler;
    private XmlGeneratorDocumentStrategyFactory $xmlGeneratorDocumentStrategyFactory;
    private XmlGeneratorTypeStrategyFactory $xmlGeneratorTypeStrategyFactory;

    public function __construct()
    {
        $this->xmlClassModeler = new XmlClassModeler();
        $this->xmlGeneratorDocumentStrategyFactory = new XmlGeneratorDocumentStrategyFactory();
        $this->xmlGeneratorTypeStrategyFactory = new XmlGeneratorTypeStrategyFactory($this->xmlClassModeler);
    }

    public function generate(string $address, string $bindingId, Service $service): DOMDocument
    {
        $wsdlDocument = new DOMDocument('1.0', 'UTF-8');
        $wsdlDocument->formatOutput = true;

        $class = $service->getClass();
        $classShortName = $class->getImplementorReflectionClass()->getShortName();
        $webServiceAttribute = $class->getWebServiceAttribute();
        $className = $webServiceAttribute->name() ?: $classShortName;
        $targetNamespace = $this->getTargetNamespace($webServiceAttribute->targetNamespace(), $class->getImplementorReflectionClass());
        $serviceName = $webServiceAttribute->serviceName() ?: "{$classShortName}Service";
        $portName = $webServiceAttribute->portName() ?: "{$classShortName}Port";
        $SOAPBindingAttribute = $class->getSOAPBindingAttribute();
        $methods = $service->getMethods();

        $definitionsElement = $wsdlDocument->createElement('definitions');
        $definitionsElement->setAttribute('xmlns:wsam', 'http://www.w3.org/2007/05/addressing/metadata');
        $definitionsElement->setAttribute("xmlns:{$bindingId}", "http://schemas.xmlsoap.org/wsdl/{$bindingId}/");
        $definitionsElement->setAttribute('xmlns:tns', $targetNamespace);
        $definitionsElement->setAttribute('xmlns:xsd', 'http://www.w3.org/2001/XMLSchema');
        $definitionsElement->setAttribute('xmlns', 'http://schemas.xmlsoap.org/wsdl/');
        $definitionsElement->setAttribute('targetNamespace', $targetNamespace);
        $definitionsElement->setAttribute('name', $serviceName);

        $typesElement = $wsdlDocument->createElement('types');
        $definitionsElement->appendChild($typesElement);

        $xsSchemaElement = $wsdlDocument->createElement('xsd:schema');
        $xsSchemaElement->setAttribute('targetNamespace', $targetNamespace);
        $typesElement->appendChild($xsSchemaElement);

        $this->xmlGeneratorTypeStrategyFactory
            ->create($SOAPBindingAttribute->style())
            ->generate($service, $wsdlDocument, $xsSchemaElement);

        $this->xmlGeneratorDocumentStrategyFactory
            ->create($SOAPBindingAttribute->style())
            ->generate($service, $wsdlDocument, $definitionsElement);

        $portTypeElement = $wsdlDocument->createElement('portType');
        $portTypeElement->setAttribute('name', $className);
        $definitionsElement->appendChild($portTypeElement);

        foreach ($methods as $method) {
            $webMethodAttribute = $method->getWebMethodAttribute();

            $name = $method->getOperationName();

            $operationElement = $wsdlDocument->createElement('operation');
            $operationElement->setAttribute('name', $name);
            $portTypeElement->appendChild($operationElement);

            $operationInputElement = $wsdlDocument->createElement('input');
            $action = "{$targetNamespace}/{$className}/{$name}Request";
            if (!is_null($webMethodAttribute)) {
                $action = $webMethodAttribute->action();
            }
            $operationInputElement->setAttribute('wsam:Action', $action);
            $operationInputElement->setAttribute('message', "tns:{$name}");
            $operationElement->appendChild($operationInputElement);

            $operationOutputElement = $wsdlDocument->createElement('output');
            $operationOutputElement->setAttribute('wsam:Action', "{$targetNamespace}/{$className}/{$name}Response");
            $operationOutputElement->setAttribute('message', "tns:{$name}Response");
            $operationElement->appendChild($operationOutputElement);
        }

        $bindingElement = $wsdlDocument->createElement('binding');
        $bindingElement->setAttribute('name', "{$portName}Binding");
        $bindingElement->setAttribute('type', "tns:{$className}");
        $definitionsElement->appendChild($bindingElement);

        $soapBindingElement = $wsdlDocument->createElement("{$bindingId}:binding");
        $soapBindingElement->setAttribute('transport', 'http://schemas.xmlsoap.org/soap/http');
        $soapBindingElement->setAttribute('style', strtolower($SOAPBindingAttribute->style()));
        $bindingElement->appendChild($soapBindingElement);

        foreach ($methods as $method) {
            $webMethodAttribute = $method->getWebMethodAttribute();

            $name = $method->getOperationName();
            $action = $webMethodAttribute?->action();

            $operationElement = $wsdlDocument->createElement('operation');
            $operationElement->setAttribute('name', $name);
            $bindingElement->appendChild($operationElement);

            $soapOperation = $wsdlDocument->createElement("{$bindingId}:operation");
            $soapOperation->setAttribute('soapAction', $action);
            $operationElement->appendChild($soapOperation);

            $operationInputElement = $wsdlDocument->createElement('input');
            $operationElement->appendChild($operationInputElement);

            $soapBodyElement = $wsdlDocument->createElement("{$bindingId}:body");
            $soapBodyElement->setAttribute('use', 'literal');
            if ($SOAPBindingAttribute->style() === SOAPBindingStyle::RPC) {
                $soapBodyElement->setAttribute('namespace', $targetNamespace);
            }
            $operationInputElement->appendChild($soapBodyElement);

            $operationOutputElement = $wsdlDocument->createElement('output');
            $operationElement->appendChild($operationOutputElement);

            $soapBodyElement = $wsdlDocument->createElement("{$bindingId}:body");
            $soapBodyElement->setAttribute('use', 'literal');
            if ($SOAPBindingAttribute->style() === SOAPBindingStyle::RPC) {
                $soapBodyElement->setAttribute('namespace', $targetNamespace);
            }
            $operationOutputElement->appendChild($soapBodyElement);
        }

        $serviceElement = $wsdlDocument->createElement('service');
        $serviceElement->setAttribute('name', $serviceName);
        $definitionsElement->appendChild($serviceElement);

        $portElement = $wsdlDocument->createElement('port');
        $portElement->setAttribute('name', $portName);
        $portElement->setAttribute('binding', "tns:{$portName}Binding");
        $serviceElement->appendChild($portElement);

        $soapAddressElement = $wsdlDocument->createElement("{$bindingId}:address");
        $soapAddressElement->setAttribute('location', $address);
        $portElement->appendChild($soapAddressElement);

        $wsdlDocument->appendChild($definitionsElement);
        return $wsdlDocument;
    }

    private function getTargetNamespace(?string $targetNamespace, ReflectionClass $class): string
    {
        if (is_null($targetNamespace)) {
            $fullName = strtolower($class->getName());
            $targetNamespace = collect(explode('\\', $fullName))
                ->reverse()
                ->implode('.');
        }

        if (!filter_var($targetNamespace, FILTER_VALIDATE_URL)) {
            $targetNamespace = "urn:{$targetNamespace}";
        }

        return $targetNamespace;
    }
}
