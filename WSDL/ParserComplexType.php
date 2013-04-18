<?php
/**
 * ParserComplexType
 *
 * @author Piotr Olaszewski
 */
namespace WSDL;

class ParserComplexType
{
    private $_parsedMethods;
    private $_methodWithComplexTypes = array();

    public function __construct($parsedMethods)
    {
        $this->_parsedMethods = $parsedMethods;
    }

    public function getComplexTypes()
    {
        foreach ($this->_parsedMethods as $methodName => $values) {
            $complexData = $this->_findComplex($methodName, $values['params']);
            $this->_methodWithComplexTypes = array_merge($this->_methodWithComplexTypes, $complexData);
        }

        return $this->_methodWithComplexTypes;
    }

    private function _findComplex($methodName, $params)
    {
        $complexArray = array();
        foreach ($params as $name => $param) {
            if (array_key_exists('array', $param)) {
                $complexArray[$methodName][$name] = $param;
            }
        }

        return $complexArray;
    }
}