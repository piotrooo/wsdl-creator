<?php
require 'vendor/autoload.php';

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Ouzo\Utilities\Path;
use WSDL\Annotation\WebService;
use WSDL\Annotation\BindingType;

/**
 * @WebService()
 * @BindingType
 */
class S
{
    public function one()
    {
        
    }
}

AnnotationRegistry::registerAutoloadNamespace('WSDL\Annotation', Path::join(__DIR__, 'src'));

$annotationReader = new AnnotationReader();
print_r($annotationReader->getClassAnnotations(new ReflectionClass('S')));
