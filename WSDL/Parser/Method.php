<?php
/**
 * Method
 *
 * @author Piotr Olaszewski
 */
namespace WSDL\Parser;

class Method
{
    private $_name;
    private $_doc;

    public function __construct($name, $doc)
    {
        $this->_name = $name;
        $this->_doc = $doc;
    }

    public function description()
    {
        preg_match('#@desc(.+)#', $this->_doc, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches);
        return !empty($trimGroupMatches[1]) ? $trimGroupMatches[1] : '';
    }

    public function parameters()
    {
        preg_match_all('#@param(.+)#', $this->_doc, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches[1]);
        $return = array();
        foreach ($trimGroupMatches as $one) {
            $pairTypeNameAndComplex = explode(' ', $one);
            $type = trim($pairTypeNameAndComplex[0]);
            $name = str_replace('$', '', $pairTypeNameAndComplex[1]);
            if ($type == 'array') {
                $arrayElements = array_splice($pairTypeNameAndComplex, 2);
                $parsedArrayProperies = $this->_parseArray($arrayElements);
                $return[$name][$type] = $parsedArrayProperies;
            } else {
                $return[][$type] = $name;
            }
        }
        return $return;
    }

    private function _parseArray($toParse)
    {
        $arrayData = array();
        foreach ($toParse as $properties) {
            preg_match('#@(.+)=(.+)#', $properties, $matches);
            $type = $matches[1];
            $value = $matches[2];
            $arrayData[$type] = $value;
        }
        return $arrayData;
    }

    public function returning()
    {
        preg_match('#@return(.+)#', $this->_doc, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches);
        return $trimGroupMatches[1];
    }

    public function getDoc()
    {
        return $this->_doc;
    }

    public function getName()
    {
        return $this->_name;
    }
}