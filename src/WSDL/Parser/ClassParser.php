<?php
/**
 * ClassParser
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Parser;

use ReflectionClass;
use ReflectionMethod;

class ClassParser
{
    private $_reflectedClass;
    /**
     * @var MethodParser[]
     */
    private $_methodDocComments = array();

    public function __construct($className)
    {
        $this->_reflectedClass = new ReflectionClass($className);
    }

    public function parse()
    {
        $this->_getAllPublicMethodDocComment();
    }

    private function _getAllPublicMethodDocComment()
    {
        $reflectionClassMethods = $this->_reflectedClass->getMethods();
        foreach ($reflectionClassMethods as $method) {
            if ($this->_checkCanParseMethod($method)) {
                $methodName = $method->getName();
                $methodDocComment = $method->getDocComment();
                $this->_methodDocComments[] = new MethodParser($methodName, $methodDocComment);
            }
        }
        return $this;
    }

    private function _checkCanParseMethod(ReflectionMethod $method)
    {
        return
            strpos($method->getDocComment(), '@WebMethod') !== false &&
            $method->isPublic() &&
            !$method->isConstructor() &&
            !$method->isDestructor() &&
            strpos($method->getName(), '__') === false;
    }

    public function getMethods()
    {
        return $this->_methodDocComments;
    }
}