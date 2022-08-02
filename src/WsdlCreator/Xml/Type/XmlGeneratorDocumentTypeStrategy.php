<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Xml\Type;

use DOMDocument;
use DOMElement;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Object_;
use WsdlCreator\Internal\Model\Service;
use WsdlCreator\Xml\Utils\XmlHelpers;
use WsdlCreator\Xml\XmlClassModeler;

/**
 * @author Piotr Olaszewski
 */
class XmlGeneratorDocumentTypeStrategy implements XmlGeneratorTypeStrategy
{
    public function __construct(private XmlClassModeler $xmlClassModeler)
    {
    }

    public function generate(Service $service, DOMDocument $wsdlDocument, DOMElement $xsSchemaElement): void
    {
        $implementorReflectionClass = $service->getClass()->getImplementorReflectionClass();
        $methods = $service->getMethods();
        foreach ($methods as $method) {
            $webMethodAttribute = $method->getWebMethodAttribute();

            $name = $webMethodAttribute?->operationName() ?: $method->getReflectionMethod()->getName();

            $xsElementElement = $wsdlDocument->createElement('xsd:element');
            $xsElementElement->setAttribute('name', $name);
            $xsElementElement->setAttribute('type', "tns:{$name}");
            $xsSchemaElement->appendChild($xsElementElement);

            $xsElementElement = $wsdlDocument->createElement('xsd:element');
            $xsElementElement->setAttribute('name', "{$name}Response");
            $xsElementElement->setAttribute('type', "tns:{$name}Response");
            $xsSchemaElement->appendChild($xsElementElement);
        }

        foreach ($methods as $method) {
            $webMethodAttribute = $method->getWebMethodAttribute();

            $name = $webMethodAttribute?->operationName() ?: $method->getReflectionMethod()->getName();

            $xsComplexTypeElement = $wsdlDocument->createElement('xsd:complexType');
            $xsComplexTypeElement->setAttribute('name', $name);
            $xsSchemaElement->appendChild($xsComplexTypeElement);

            $classes = [];

            $xsSequenceElement = $wsdlDocument->createElement('xsd:sequence');
            $parameters = $method->getParameters();
            if (!empty($parameters)) {
                foreach ($parameters as $i => $parameter) {
                    $paramName = "arg{$i}";
                    $type = '';
                    $isArray = false;
                    $reflectionUnionType = $parameter->getReflectionParameter()->getType();
                    if ($reflectionUnionType->isBuiltin()) {
                        $parameterType = $reflectionUnionType->getName();
                        if (!in_array($parameterType, ['array', 'class'])) {
                            $mapPhpTypeToWsdl = XmlHelpers::mapPhpTypeToWsdl($parameterType);
                            $type = "xsd:{$mapPhpTypeToWsdl}";
                        } else if ($parameterType === 'array') {
                            $isArray = true;

                            $arrayParameter = $parameter->getParam();
                            $docType = $arrayParameter->getType();
                            if ($docType instanceof Array_) {
                                $valueType = $docType->getValueType();
                                if ($valueType instanceof Object_) {
                                    $fqdnClass = XmlHelpers::findFqdnClass($implementorReflectionClass, $valueType->getFqsen());
                                    $type = XmlHelpers::classType($fqdnClass);

                                    $classes[] = $fqdnClass;
                                } else {
                                    $mapPhpTypeToWsdl = XmlHelpers::mapPhpTypeToWsdl($parameterType);
                                    $type = "xsd:{$mapPhpTypeToWsdl}";
                                }
                            }
                        }
                    } else {
                        $type = XmlHelpers::classType($reflectionUnionType->getName());

                        $classes[] = $reflectionUnionType->getName();
                    }

                    $xsElementElement = $wsdlDocument->createElement('xsd:element');
                    $xsElementElement->setAttribute('name', $paramName);
                    $xsElementElement->setAttribute('type', $type);
                    $xsElementElement->setAttribute('minOccurs', '0');
                    if ($isArray) {
                        $xsElementElement->setAttribute('maxOccurs', 'unbounded');
                    }
                    $xsSequenceElement->appendChild($xsElementElement);
                }
            }
            $xsComplexTypeElement->appendChild($xsSequenceElement);

            foreach ($classes as $class) {
                $this->xmlClassModeler->append($class, $wsdlDocument, $xsSchemaElement);
            }

            $xsComplexTypeElement = $wsdlDocument->createElement('xsd:complexType');
            $xsComplexTypeElement->setAttribute('name', "{$name}Response");
            $xsSchemaElement->appendChild($xsComplexTypeElement);

            $xsSequenceElement = $wsdlDocument->createElement('xsd:sequence');
            $classes = [];
            $reflectionUnionType = $method->getReturn()->getReflectionType();
            $name = 'return';
            if ($reflectionUnionType->getName() !== 'void') {
                $type = '';
                $isArray = false;
                if ($reflectionUnionType->isBuiltin()) {
                    $parameterType = $reflectionUnionType->getName();
                    if (!in_array($parameterType, ['array', 'class'])) {
                        $mapPhpTypeToWsdl = XmlHelpers::mapPhpTypeToWsdl($parameterType);
                        $type = "xsd:{$mapPhpTypeToWsdl}";
                    } else if ($parameterType === 'array') {
                        $isArray = true;

                        /** @var Param|null $arrayParameter */
                        $arrayParameter = $method->getReturn()->getReturn();
                        $docType = $arrayParameter->getType();
                        if ($docType instanceof Array_) {
                            $valueType = $docType->getValueType();
                            if ($valueType instanceof Object_) {
                                $fqdnClass = XmlHelpers::findFqdnClass($implementorReflectionClass, $valueType->getFqsen());
                                $type = XmlHelpers::classType($fqdnClass);

                                $classes[] = $fqdnClass;
                            } else {
                                $mapPhpTypeToWsdl = XmlHelpers::mapPhpTypeToWsdl($parameterType);
                                $type = "xsd:{$mapPhpTypeToWsdl}";
                            }
                        }
                    }
                } else {
                    $type = XmlHelpers::classType($reflectionUnionType->getName());

                    $classes[] = $reflectionUnionType->getName();
                }

                $xsElementElement = $wsdlDocument->createElement('xsd:element');
                $xsElementElement->setAttribute('name', $name);
                $xsElementElement->setAttribute('type', $type);
                $xsElementElement->setAttribute('minOccurs', '0');
                if ($isArray) {
                    $xsElementElement->setAttribute('maxOccurs', 'unbounded');
                }
                $xsSequenceElement->appendChild($xsElementElement);
            }

            $xsComplexTypeElement->appendChild($xsSequenceElement);

            foreach ($classes as $class) {
                $this->xmlClassModeler->append($class, $wsdlDocument, $xsSchemaElement);
            }
        }
    }
}
