<?php
/**
 * Copyright (C) 2013-2015
 * Piotr Olaszewski <piotroo89@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace WSDL\Parser;

use ReflectionClass;
use ReflectionMethod;

/**
 * ClassParser
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
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