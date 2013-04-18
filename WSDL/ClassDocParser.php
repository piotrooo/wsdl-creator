<?php
/**
 * ClassParser
 *
 * @author Piotr Olaszewski
 */
namespace WSDL;
require_once 'ParserComplexType.php';

class ClassDocParser
{
    private $_reflectedClass;
    private $_methodDocComments = array();
    private $_parsedMethods = array();

    public function __construct($className)
    {
        $this->_reflectedClass = new \ReflectionClass($className);

        $this->_getAllPublicMethodDocComment();
        $this->_parseDocComment();
    }

    private function _getAllPublicMethodDocComment()
    {
        $reflectionClassMethods = $this->_reflectedClass->getMethods();

        foreach ($reflectionClassMethods as $singleMethod) {
            if ($singleMethod->isPublic()) {
                $methodName = $singleMethod->getName();
                $methodDocComment = $singleMethod->getDocComment();

                $this->_methodDocComments[$methodName] = $methodDocComment;
            }
        }
    }

    private function _parseDocComment()
    {
        foreach ($this->_methodDocComments as $methodName => $methodComment) {
            $rawTrimCommentWithoutStars = trim(str_replace(array('*', '/'), '', $methodComment));

            $desc = $this->_parseDesc($rawTrimCommentWithoutStars);
            $this->_parsedMethods[$methodName]['desc'] = $desc;

            $parameters = $this->_parseParameters($rawTrimCommentWithoutStars);
            $this->_parsedMethods[$methodName]['params'] = $parameters;

            $return = $this->_parseReturn($rawTrimCommentWithoutStars);
            $this->_parsedMethods[$methodName]['return'] = $return;
        }
    }

    private function _parseDesc($docCommentString)
    {
        preg_match('#@desc(.+)#', $docCommentString, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches);

        return !empty($trimGroupMatches[1]) ? $trimGroupMatches[1] : '';
    }

    private function _parseParameters($docCommentString)
    {
        preg_match_all('#@param(.+)#', $docCommentString, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches[1]);

        $return = array();
        foreach ($trimGroupMatches as $i => $one) {
            $pairTypeNameAndComplex = explode(' ', $one);

            $type = trim($pairTypeNameAndComplex[0]);
            $name = str_replace('$', '', $pairTypeNameAndComplex[1]);

            if ($type == 'array') {
                $parsedArrayProperies = $this->_parseArray(array_splice($pairTypeNameAndComplex, 2));

                $return[$name][$type] = $parsedArrayProperies;
            } else {
                $return[][$type] = $name;
            }
        }

        return $return;
    }

    private function _parseArray($type)
    {
        $arrayData = array();

        foreach ($type as $properties) {
            preg_match('#@(.+)=(.+)#', $properties, $matches);
            $param = $matches[1];
            $value = $matches[2];

            $arrayData[$param] = $value;
        }

        return $arrayData;
    }

    private function _parseReturn($docCommentString)
    {
        preg_match('#@return(.+)#', $docCommentString, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches);

        return $trimGroupMatches[1];
    }

    public function getAllMethods()
    {
        return array_keys($this->_parsedMethods);
    }

    public function getParsedComments()
    {
        return $this->_parsedMethods;
    }

    public function parserComplexTypes()
    {
        return new ParserComplexType($this->_parsedMethods);
    }
}