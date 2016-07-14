<?php
require 'vendor/autoload.php';

/**
 * @WebService(name="", targetNamespace="", location="")
 * @SoapBinding(style="RPC|DOCUMENT", use="LITERAL|ENCODED", parameterStyle="BARE|WRAPPED")
 * @BindingType(value="SOAP_11|SOAP_12")
 */
class Service
{
    /**
     * @WebMethod
     * @WebParam(param="int $a", header="")
     * @WebParam(param="int $b", header="")
     * @WebResult(param="int $result")
     */
    public function sum($a, $b)
    {
    }

    /**
     * @WebMethod
     * @WebParam(
     *     param="object $object { string $name int $age }",
     *     header=""
     * )
     * @WebResult(param="object $result { int $code string $message }")
     */
    public function transformObject($object)
    {
    }
}

/**
 * @Annotation
 */
final class WebService
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $targetNamespace;
    /**
     * @var string
     */
    public $location;
}

/**
 * @Annotation
 * @Target("METHOD")
 */
final class WebMethod
{
}

/**
 * @WebService(name="n", targetNamespace="tn", location="l")
 */
class S
{
    /**
     * @WebMethod
     */
    public function sum($a, $b)
    {
    }
}

$annotationReader = new \Doctrine\Common\Annotations\AnnotationReader();
print_r($annotationReader->getClassAnnotations(new ReflectionClass('S')));
print_r($annotationReader->getMethodAnnotations(new ReflectionMethod('S', 'sum')));
