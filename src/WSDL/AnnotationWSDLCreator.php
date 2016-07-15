<?php
namespace WSDL;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Ouzo\Utilities\Path;
use ReflectionClass;

class AnnotationWSDLCreator
{
    private $class;

    public function __construct($class)
    {
        AnnotationRegistry::registerAutoloadNamespace('WSDL\Annotation', Path::join(__DIR__, '..'));
        $this->class = $class;
        $annotationReader = new AnnotationReader();
        $annotationReader->getClassAnnotations($this->reflectionClass());
    }

    private function reflectionClass()
    {
        return new ReflectionClass($this->class);
    }
}
