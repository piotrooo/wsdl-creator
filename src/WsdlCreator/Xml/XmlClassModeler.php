<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Xml;

use DOMDocument;
use DOMElement;
use ReflectionClass;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Type;
use WsdlCreator\Xml\Utils\XmlHelpers;

/**
 * @author Piotr Olaszewski
 */
class XmlClassModeler
{
    private PropertyInfoExtractor $propertyInfoExtractor;

    /** @var string[] */
    private array $generated = [];

    public function __construct()
    {
        $reflectionExtractor = new ReflectionExtractor();
        $phpDocExtractor = new PhpDocExtractor();
        $listExtractors = [$reflectionExtractor];
        $typeExtractors = [$phpDocExtractor, $reflectionExtractor];
        $descriptionExtractors = [$phpDocExtractor];
        $accessExtractors = [$reflectionExtractor];
        $propertyInitializableExtractors = [$reflectionExtractor];

        $this->propertyInfoExtractor = new PropertyInfoExtractor(
            $listExtractors, $typeExtractors, $descriptionExtractors, $accessExtractors, $propertyInitializableExtractors
        );
    }

    public function append(string $class, DOMDocument $wsdlDocument, DOMElement $xsSchemaElement): void
    {
        if (in_array($class, $this->generated)) {
            return;
        }
        $this->generated[] = $class;

        $classesToGenerate = [];

        $reflectionClass = new ReflectionClass($class);
        $shortName = strtolower($reflectionClass->getShortName());

        $xsComplexTypeElement = $wsdlDocument->createElement('xsd:complexType');
        $xsComplexTypeElement->setAttribute('name', $shortName);
        $xsSchemaElement->appendChild($xsComplexTypeElement);

        $xsSequenceElement = $wsdlDocument->createElement('xsd:sequence');
        $xsComplexTypeElement->appendChild($xsSequenceElement);

        $properties = $this->propertyInfoExtractor->getProperties($class);
        $properties = collect($properties)->sort();
        foreach ($properties as $property) {
            $types = $this->propertyInfoExtractor->getTypes($class, $property);
            /** @var Type $type */
            $type = collect($types)->first();

            $xsElementElement = $wsdlDocument->createElement('xsd:element');
            $xsElementElement->setAttribute('name', $property);
            if ($type->isCollection()) {
                /** @var Type $valueType */
                $valueType = collect($type->getCollectionValueTypes())->first();
                $className = $valueType->getClassName();
                if (is_null($className)) {
                    $mapPhpTypeToWsdl = XmlHelpers::mapPhpTypeToWsdl($type->getBuiltinType());
                    $xsElementElement->setAttribute('type', "xsd:{$mapPhpTypeToWsdl}");
                } else {
                    $reflectionClass = new ReflectionClass($className);
                    $classesToGenerate[] = $className;
                    $shortName = strtolower($reflectionClass->getShortName());
                    $xsElementElement->setAttribute('type', "tns:{$shortName}");
                }

                if ($type->isNullable()) {
                    $xsElementElement->setAttribute('nillable', 'true');
                }
                $xsElementElement->setAttribute('minOccurs', '0');
                $xsElementElement->setAttribute('maxOccurs', 'unbounded');
            } else {
                $className = $type->getClassName();
                if (is_null($className)) {
                    $mapPhpTypeToWsdl = XmlHelpers::mapPhpTypeToWsdl($type->getBuiltinType());
                    $xsElementElement->setAttribute('type', "xsd:{$mapPhpTypeToWsdl}");
                    if ($type->isNullable()) {
                        $xsElementElement->setAttribute('minOccurs', '0');
                    }
                } else {
                    $classesToGenerate[] = $className;

                    $reflectionClass = new ReflectionClass($className);
                    $shortName = strtolower($reflectionClass->getShortName());
                    $xsElementElement->setAttribute('type', "tns:{$shortName}");
                    if ($type->isNullable()) {
                        $xsElementElement->setAttribute('nillable', 'true');
                    }
                    $xsElementElement->setAttribute('minOccurs', '0');
                }
            }
            $xsSequenceElement->appendChild($xsElementElement);
        }

        while (!empty($classesToGenerate)) {
            $classToGenerate = array_shift($classesToGenerate);
            $this->append($classToGenerate, $wsdlDocument, $xsSchemaElement);
        }
    }
}
