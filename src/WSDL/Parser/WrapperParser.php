<?php
/**
 * WrapperParser
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Parser;

use ReflectionClass;
use ReflectionProperty;
use WSDL\Types\Arrays;
use WSDL\Types\Object;

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
            $this->_makeComplexType($field->getName(), $field->getDocComment());
        }
    }

    private function _makeComplexType($name, $docComment)
    {
        if (preg_match('#@type (\w*)\[\]#', $docComment, $matches)) {
            $type = $matches[1];
            $strategy = 'array';
        } else {
            preg_match('#@type (\w+)#', $docComment, $matches);
            if (isset($matches[1])) {
                $type = trim($matches[1]);
                $strategy = trim($matches[1]);
            } else {
                $type = 'void';
                $strategy = 'void';
            }
        }

        switch ($strategy) {
            case 'object':
                $this->_complexTypes[] = Object($type, $name, $this->getComplexTypes());
                break;
            case 'wrapper':
                $this->_complexTypes[] = $this->_createWrapperObject($type, $name, $docComment);
                break;
            case 'array':
                $this->_complexTypes[] = $this->_createArrayObject($type, $name, $docComment);
                break;
            default:
                $this->_complexTypes[] = new ComplexTypeParser($type, $name);
                break;
        }
    }
    
    private function _createWrapperObject($type, $name, $docComment)
    {
        $wrapper = $this->wrapper($type, $docComment);
        $object = null;
        if ($wrapper->getComplexTypes()) {
            $object = new Object($type, $name, $wrapper->getComplexTypes());
        }
        return new Object($type, $name, $object);
    }

    private function _createArrayObject($type, $name, $docComment)
    {
        $object = null;
        if ($type == 'wrapper') {
            $complex = $this->wrapper($type, $docComment)->getComplexTypes();
            $object = new Object($type, $name, $complex);
        } else if ($this->isComplex($type)) {
            $complex = $this->getComplexTypes();
            $object = new Object($type, $name, $complex);
        }
        return new Arrays($type, $name, $object);
    }

    public function getComplexTypes()
    {
        return $this->_complexTypes;
    }
    
    public function wrapper(&$type, $docComment)
    {
        if (!$this->isComplex($type)) {
            throw new WrapperParserException("This attribute is not complex type.");
        }
        preg_match('#@className=(.*?)(?:\s|$)#', $docComment, $matches);
        $className = $matches[1];
        $type = str_replace('\\', '', $className);
        $wrapperParser = new WrapperParser($className);
        $wrapperParser->parse();
        return $wrapperParser;
    }

    public function isComplex($type)
    {
        return in_array($type, array('object', 'wrapper'));
    }
}