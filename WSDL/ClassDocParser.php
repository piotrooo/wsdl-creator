<?php
/**
 * ClassParser
 *
 * @author Piotrooo
 */
namespace WSDL;

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

    public function _parseDocComment()
    {
        foreach ($this->_methodDocComments as $single => $comment) {
            $rawTrimCommentWithoutStars = trim(str_replace(array('*', '/'), '', $comment));

            $desc = $this->_parseDesc($rawTrimCommentWithoutStars);
            $this->_parsedMethods[$single]['desc'] = $desc;

            $parameters = $this->_parseParameters($rawTrimCommentWithoutStars);
            $this->_parsedMethods[$single]['params'] = $parameters;

            $return = $this->_parseReturn($rawTrimCommentWithoutStars);
            $this->_parsedMethods[$single]['return'] = $return;
        }

        print_r($this->_parsedMethods);
    }

    public function _parseDesc($docCommentString)
    {
        preg_match('#@desc(.+)#', $docCommentString, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches);

        return $trimGroupMatches[1];
    }

    public function _parseParameters($docCommentString)
    {
        preg_match_all('#@param(.+)#', $docCommentString, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches[1]);
        $return = array();

        foreach ($trimGroupMatches as $one) {
            $pairTypeAndName = explode(' ', $one);

            $return[][trim($pairTypeAndName[0])] = $pairTypeAndName[1];
        }

        return $return;
    }

    private function _parseReturn($docCommentString)
    {
        preg_match('#@return(.+)#', $docCommentString, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches);

        return $trimGroupMatches[1];
    }
}