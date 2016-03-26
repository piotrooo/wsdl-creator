<?php
/**
 * Copyright (C) 2013-2016
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

use Ouzo\Utilities\Strings;
use ReflectionClass;
use ReflectionMethod;

/**
 * ClassParser
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class ClassParser
{
    /**
     * @var ReflectionClass
     */
    private $reflectedClass;
    /**
     * @var MethodParser[]
     */
    private $methodDocComments = array();

    public function __construct($className)
    {
        $this->reflectedClass = new ReflectionClass($className);
    }

    public function parse()
    {
        $this->allPublicMethodDocComment();
    }

    private function allPublicMethodDocComment()
    {
        $reflectionClassMethods = $this->reflectedClass->getMethods();
        foreach ($reflectionClassMethods as $reflectionMethod) {
            if ($this->canParseMethod($reflectionMethod)) {
                $methodName = $reflectionMethod->getName();
                $methodDocComment = $reflectionMethod->getDocComment();
                $this->methodDocComments[] = new MethodParser($methodName, $methodDocComment);
            }
        }
    }

    /**
     * @param ReflectionMethod $method
     * @return bool
     */
    private function canParseMethod(ReflectionMethod $method)
    {
        return
            Strings::contains($method->getDocComment(), '@WebMethod') &&
            $method->isPublic() &&
            !$method->isConstructor() &&
            !$method->isDestructor() &&
            !Strings::contains($method->getName(), '__');
    }

    /**
     * @return MethodParser[]
     */
    public function getMethods()
    {
        return $this->methodDocComments;
    }
}
