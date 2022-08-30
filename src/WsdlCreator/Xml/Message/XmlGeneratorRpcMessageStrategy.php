<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Xml\Message;

use DOMDocument;
use DOMElement;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Object_;
use WsdlCreator\Annotation\SOAPBindingStyle;
use WsdlCreator\Internal\Model\Service;
use WsdlCreator\Xml\Utils\XmlHelpers;

/**
 * @author Piotr Olaszewski
 */
class XmlGeneratorRpcMessageStrategy implements XmlGeneratorMessageStrategy
{
    public function generate(Service $service, DOMDocument $wsdlDocument, DOMElement $definitionsElement): void
    {
        $implementorReflectionClass = $service->getClass()->getImplementorReflectionClass();
        $methods = $service->getMethods();
        foreach ($methods as $method) {
            $name = $method->getOperationName();

            $messageElement = $wsdlDocument->createElement('message');
            $messageElement->setAttribute('name', $name);
            $definitionsElement->appendChild($messageElement);

            foreach ($method->getParameters() as $i => $parameter) {
                $type = '';
                $reflectionUnionType = $parameter->getReflectionParameter()->getType();
                if ($reflectionUnionType->isBuiltin()) {
                    $parameterType = $reflectionUnionType->getName();
                    if (!in_array($parameterType, ['array', 'class'])) {
                        $mapPhpTypeToWsdl = XmlHelpers::mapPhpTypeToWsdl($parameterType);
                        $type = "xsd:{$mapPhpTypeToWsdl}";
                    } else if ($parameterType === 'array') {
                        $arrayParameter = $parameter->getParam();
                        $docType = $arrayParameter->getType();
                        if ($docType instanceof Array_) {
                            $valueType = $docType->getValueType();
                            if ($valueType instanceof Object_) {
                                $fqdnClass = XmlHelpers::findFqdnClass($implementorReflectionClass, $valueType->getFqsen());
                                $type = XmlHelpers::classType($fqdnClass);
                                $type = "{$type}Array";
                            } else {
                                $mapPhpTypeToWsdl = XmlHelpers::mapPhpTypeToWsdl($valueType->__toString());
                                $type = "tns:{$mapPhpTypeToWsdl}Array";
                            }
                        }
                    }
                } else {
                    $type = XmlHelpers::classType($reflectionUnionType->getName());
                }

                $paramName = $parameter->getName($i, SOAPBindingStyle::RPC);

                $partElement = $wsdlDocument->createElement('part');
                $partElement->setAttribute('name', $paramName);
                $partElement->setAttribute('type', $type);
                $messageElement->appendChild($partElement);
            }

            $messageResponseElement = $wsdlDocument->createElement('message');
            $messageResponseElement->setAttribute('name', "{$name}Response");
            $definitionsElement->appendChild($messageResponseElement);

            $reflectionUnionType = $method->getReturn()->getReflectionType();
            $parameterType = $reflectionUnionType->getName();
            if ($parameterType !== 'void') {
                $type = '';
                if ($reflectionUnionType->isBuiltin()) {
                    if (!in_array($parameterType, ['array', 'class'])) {
                        $mapPhpTypeToWsdl = XmlHelpers::mapPhpTypeToWsdl($parameterType);
                        $type = "xsd:{$mapPhpTypeToWsdl}";
                    } else if ($parameterType === 'array') {
                        $docType = $method->getReturn()->getReturn()->getType();
                        if ($docType instanceof Array_) {
                            $valueType = $docType->getValueType();
                            if ($valueType instanceof Object_) {
                                $fqdnClass = XmlHelpers::findFqdnClass($implementorReflectionClass, $valueType->getFqsen());
                                $type = XmlHelpers::classType($fqdnClass);
                                $type = "{$type}Array";
                            } else {
                                $mapPhpTypeToWsdl = XmlHelpers::mapPhpTypeToWsdl($parameterType);
                                $type = "tns:{$mapPhpTypeToWsdl}Array";
                            }
                        }
                    }
                } else {
                    $type = XmlHelpers::classType($parameterType);
                }

                $partResponseElement = $wsdlDocument->createElement('part');
                $partResponseElement->setAttribute('name', 'return');
                $partResponseElement->setAttribute('type', $type);
                $messageResponseElement->appendChild($partResponseElement);
            }
        }
    }
}
