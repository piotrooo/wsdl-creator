<?php
namespace WSDL\XML;

use DOMDocument;

class XMLAttributeHelper
{
    private $DOMDocument;

    public function __construct(DOMDocument $DOMDocument)
    {
        $this->DOMDocument = $DOMDocument;
    }

    public function createElementWithAttributes($elementName, $attributes, $value = '')
    {
        $element = $this->createElement($elementName, $value);
        foreach ($attributes as $attributeName => $attributeValue) {
            $tmpAttr = $this->createAttributeWithValue($attributeName, $attributeValue);
            $element->appendChild($tmpAttr);
        }
        return $element;
    }

    public function createElement($elementName, $value = '')
    {
        return $this->DOMDocument->createElement($elementName, $value);
    }

    public function createAttributeWithValue($attributeName, $value)
    {
        $attribute = $this->DOMDocument->createAttribute($attributeName);
        $attribute->value = $value;
        return $attribute;
    }

    public static function forDOM(DOMDocument $DOMDocument)
    {
        return new self($DOMDocument);
    }
}
