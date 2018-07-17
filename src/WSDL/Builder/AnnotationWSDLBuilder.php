<?php
/**
 * Copyright (C) 2013-2018
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
namespace WSDL\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Exception;
use Ouzo\Utilities\Path;
use ReflectionClass;
use WSDL\Annotation\ClassAnnotation;
use WSDL\Annotation\MethodAnnotation;

/**
 * AnnotationWSDLBuilder
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class AnnotationWSDLBuilder
{
    /**
     * @var string
     */
    private $class;
    /**
     * @var WSDLBuilder
     */
    private $builder;
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(string $class)
    {
        AnnotationRegistry::registerAutoloadNamespace('WSDL\Annotation', Path::join(__DIR__, '..', '..'));
        $this->class = $class;
        $this->builder = WSDLBuilder::instance();
        $this->annotationReader = new AnnotationReader();
    }

    private function reflectionClass(): ReflectionClass
    {
        return new ReflectionClass($this->class);
    }

    public function build(): AnnotationWSDLBuilder
    {
        if (!class_exists($this->class)) {
            throw new Exception('Class [' . $this->class . '] not exists');
        }
        $this->buildForClass();
        $this->buildForMethods();

        return $this;
    }

    private function buildForClass(): void
    {
        $class = $this->reflectionClass();
        $webServiceAnnotation = $this->annotationReader->getClassAnnotation($class, '\WSDL\Annotation\WebService');
        if ($webServiceAnnotation === null) {
            throw new AnnotationBuilderException('Class must have @WebService annotation');
        }
        /** @var ClassAnnotation[] $classAnnotations */
        $classAnnotations = $this->annotationReader->getClassAnnotations($class);
        foreach ($classAnnotations as $classAnnotation) {
            $classAnnotation->build($this->builder, $class);
        }
    }

    private function buildForMethods(): void
    {
        $methods = [];
        $classMethods = $this->reflectionClass()->getMethods();
        foreach ($classMethods as $classMethod) {
            $webMethodAnnotation = $this->annotationReader->getMethodAnnotation($classMethod, '\WSDL\Annotation\WebMethod');
            if ($webMethodAnnotation === null) {
                continue;
            }
            $methodBuilder = MethodBuilder::instance();
            /** @var MethodAnnotation[] $methodAnnotations */
            $methodAnnotations = $this->annotationReader->getMethodAnnotations($classMethod);
            foreach ($methodAnnotations as $methodAnnotation) {
                $methodAnnotation->build($methodBuilder, $classMethod);
            }
            $methods[] = $methodBuilder->build();
        }
        $this->builder->setMethods($methods);
    }

    public function getBuilder(): WSDLBuilder
    {
        return $this->builder;
    }
}
