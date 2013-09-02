<?php
/**
 * ClassParser
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Parser;

use ReflectionClass;
use WSDL\Parser\MethodParser;

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
            if ($method->isPublic()) {
                $methodName = $method->getName();
                $methodDocComment = $method->getDocComment();
                $this->_methodDocComments[] = new MethodParser($methodName, $methodDocComment);
            }
        }
        return $this;
    }

    public function getMethods()
    {
        return $this->_methodDocComments;
    }
}