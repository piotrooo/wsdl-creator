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
class XmlGeneratorRpcTypeStrategy implements XmlGeneratorTypeStrategy
{
    public function __construct(private XmlClassModeler $xmlClassModeler)
    {
    }

    public function generate(Service $service, DOMDocument $wsdlDocument, DOMElement $xsSchemaElement): void
    {
        $generated = [];

        $implementorReflectionClass = $service->getClass()->getImplementorReflectionClass();
        $methods = $service->getMethods();
        foreach ($methods as $method) {
            foreach ($method->getParameters() as $parameter) {
                $reflectionUnionType = $parameter->getReflectionParameter()->getType();
                if ($reflectionUnionType->isBuiltin()) {
                    if ($reflectionUnionType->getName() === 'array') {
                        $arrayParameter = $parameter->getParam();;
                        $docType = $arrayParameter->getType();
                        if ($docType instanceof Array_) {
                            $valueType = $docType->getValueType();
                            if ($valueType instanceof Object_) {
                                $fqdnClass = XmlHelpers::findFqdnClass($implementorReflectionClass, $valueType->getFqsen());
                                $type = XmlHelpers::classType($fqdnClass);
                                $name = str_replace('tns:', '', "{$type}Array");

                                if (in_array($name, $generated)) {
                                    continue;
                                }
                                $generated[] = $name;

                                $xsComplexTypeElement = $wsdlDocument->createElement('xsd:complexType');
                                $xsComplexTypeElement->setAttribute('name', $name);
                                $xsComplexTypeElement->setAttribute('final', "#all");
                                $xsSchemaElement->appendChild($xsComplexTypeElement);

                                $xsSequenceElement = $wsdlDocument->createElement('xsd:sequence');
                                $xsComplexTypeElement->appendChild($xsSequenceElement);

                                $xsElementElement = $wsdlDocument->createElement('xsd:element');
                                $xsElementElement->setAttribute('name', 'item');
                                $xsElementElement->setAttribute('type', $type);
                                $xsElementElement->setAttribute('minOccurs', '0');
                                $xsElementElement->setAttribute('maxOccurs', 'unbounded');
                                if ($reflectionUnionType->allowsNull()) {
                                    $xsElementElement->setAttribute('nillable', 'true');
                                }
                                $xsSequenceElement->appendChild($xsElementElement);

                                $this->xmlClassModeler->append($fqdnClass, $wsdlDocument, $xsSchemaElement);
                            } else {
                                $type = XmlHelpers::mapPhpTypeToWsdl($reflectionUnionType->getName());
                                $name = "{$type}Array";

                                if (in_array($name, $generated)) {
                                    continue;
                                }
                                $generated[] = $name;

                                $xsComplexTypeElement = $wsdlDocument->createElement('xsd:complexType');
                                $xsComplexTypeElement->setAttribute('name', $name);
                                $xsComplexTypeElement->setAttribute('final', "#all");
                                $xsSchemaElement->appendChild($xsComplexTypeElement);

                                $xsSequenceElement = $wsdlDocument->createElement('xsd:sequence');
                                $xsComplexTypeElement->appendChild($xsSequenceElement);

                                $xsElementElement = $wsdlDocument->createElement('xsd:element');
                                $xsElementElement->setAttribute('name', 'item');
                                $xsElementElement->setAttribute('type', "xsd:{$type}");
                                $xsElementElement->setAttribute('minOccurs', '0');
                                $xsElementElement->setAttribute('maxOccurs', 'unbounded');
                                if ($reflectionUnionType->allowsNull()) {
                                    $xsElementElement->setAttribute('nillable', 'true');
                                }
                                $xsSequenceElement->appendChild($xsElementElement);
                            }
                        }
                    }
                } else {
                    $this->xmlClassModeler->append($reflectionUnionType->getName(), $wsdlDocument, $xsSchemaElement);
                }
            }

            $reflectionNamedType = $method->getReturn()->getReflectionType();
            $parameterType = $reflectionNamedType->getName();
            if ($parameterType !== 'void') {
                if ($reflectionNamedType->isBuiltin()) {
                    if ($parameterType === 'array') {
                        /** @var Param|null $arrayParameter */
                        $arrayParameter = $method->getReturn()->getReturn();
                        $docType = $arrayParameter->getType();
                        if ($docType instanceof Array_) {
                            $valueType = $docType->getValueType();
                            if ($valueType instanceof Object_) {
                                $fqdnClass = XmlHelpers::findFqdnClass($implementorReflectionClass, $valueType->getFqsen());
                                $type = XmlHelpers::classType($fqdnClass);
                                $name = str_replace('tns:', '', "{$type}Array");

                                if (!in_array($name, $generated)) {
                                    $generated[] = $name;

                                    $xsComplexTypeElement = $wsdlDocument->createElement('xsd:complexType');
                                    $xsComplexTypeElement->setAttribute('name', $name);
                                    $xsComplexTypeElement->setAttribute('final', "#all");
                                    $xsSchemaElement->appendChild($xsComplexTypeElement);

                                    $xsSequenceElement = $wsdlDocument->createElement('xsd:sequence');
                                    $xsComplexTypeElement->appendChild($xsSequenceElement);

                                    $xsElementElement = $wsdlDocument->createElement('xsd:element');
                                    $xsElementElement->setAttribute('name', 'item');
                                    $xsElementElement->setAttribute('type', $type);
                                    $xsElementElement->setAttribute('minOccurs', '0');
                                    $xsElementElement->setAttribute('maxOccurs', 'unbounded');
                                    if ($reflectionNamedType->allowsNull()) {
                                        $xsElementElement->setAttribute('nillable', 'true');
                                    }
                                    $xsSequenceElement->appendChild($xsElementElement);

                                    $this->xmlClassModeler->append($fqdnClass, $wsdlDocument, $xsSchemaElement);
                                }
                            } else {
                                $type = XmlHelpers::mapPhpTypeToWsdl($parameterType);
                                $name = "{$type}Array";
                                if (!in_array($name, $generated)) {
                                    $xsComplexTypeElement = $wsdlDocument->createElement('xsd:complexType');
                                    $xsComplexTypeElement->setAttribute('name', $name);
                                    $xsComplexTypeElement->setAttribute('final', "#all");
                                    $xsSchemaElement->appendChild($xsComplexTypeElement);

                                    $xsSequenceElement = $wsdlDocument->createElement('xsd:sequence');
                                    $xsComplexTypeElement->appendChild($xsSequenceElement);

                                    $xsElementElement = $wsdlDocument->createElement('xsd:element');
                                    $xsElementElement->setAttribute('name', 'item');
                                    $xsElementElement->setAttribute('type', "xsd:{$type}");
                                    $xsElementElement->setAttribute('minOccurs', '0');
                                    $xsElementElement->setAttribute('maxOccurs', 'unbounded');
                                    if ($reflectionNamedType->allowsNull()) {
                                        $xsElementElement->setAttribute('nillable', 'true');
                                    }
                                    $xsElementElement->setAttribute('nillable', 'true');
                                    $xsSequenceElement->appendChild($xsElementElement);
                                }
                            }
                        }
                    }
                } else {
                    $this->xmlClassModeler->append($parameterType, $wsdlDocument, $xsSchemaElement);
                }
            }
        }
    }
}
