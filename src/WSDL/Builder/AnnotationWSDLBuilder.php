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
namespace WSDL\Builder;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Ouzo\Utilities\Path;
use ReflectionClass;
use WSDL\Annotation\BindingType;
use WSDL\Annotation\SoapBinding;
use WSDL\Annotation\WebService;

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

    /**
     * @param string $class
     */
    public function __construct($class)
    {
        AnnotationRegistry::registerAutoloadNamespace('WSDL\Annotation', Path::join(__DIR__, '..', '..'));
        $this->class = $class;
        $this->builder = WSDLBuilder::instance();
        $this->annotationReader = new AnnotationReader();
    }

    /**
     * @return ReflectionClass
     */
    private function reflectionClass()
    {
        return new ReflectionClass($this->class);
    }

    /**
     * @return $this
     */
    public function build()
    {
        $classAnnotations = $this->annotationReader->getClassAnnotations($this->reflectionClass());
        foreach ($classAnnotations as $classAnnotation) {
            if ($classAnnotation instanceof WebService) {
                $this->builder
                    ->setName($classAnnotation->name)
                    ->setTargetNamespace($classAnnotation->targetNamespace)
                    ->setNs($classAnnotation->ns)
                    ->setLocation($classAnnotation->location);
            }
            if ($classAnnotation instanceof BindingType) {
                $this->builder->setSoapVersion($classAnnotation->value);
            }
            if ($classAnnotation instanceof SoapBinding) {
                $this->builder
                    ->setStyle($classAnnotation->style)
                    ->setUse($classAnnotation->use)
                    ->setParameterStyle($classAnnotation->parameterStyle);
            }
        }
        return $this;
    }

    /**
     * @return WSDLBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }
}
