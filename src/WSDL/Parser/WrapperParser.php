<?php
/**
 * WrapperParser
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Parser;

use ReflectionClass;
use ReflectionProperty;

class WrapperParser
{
    private $_wrapperClass;
    /**
     * @var ComplexTypeParser[]
     */
    private $_complexTypes;

    public function __construct($wrapperClass)
    {
        $this->_wrapperClass = new ReflectionClass($wrapperClass);
    }

    public function parse()
    {
        $publicFields = $this->_wrapperClass->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($publicFields as $field) {
            $type = $this->_parseDocForType($field->getDocComment());
            $this->_makeComplexType($type, $field->getName());
        }
    }

    private function _parseDocForType($docComment)
    {
        preg_match('#@type (\w+)#', $docComment, $matches);
        return trim($matches[1]);
    }

    private function _makeComplexType($type, $name)
    {
        $this->_complexTypes[] = new ComplexTypeParser($type, $name);
    }

    public function getComplexTypes()
    {
        return $this->_complexTypes;
    }
}