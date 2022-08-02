<?php

namespace WsdlCreator\Internal\Model;

use ReflectionClass;
use WsdlCreator\Annotation\SOAPBinding;
use WsdlCreator\Annotation\WebService;

class Class_
{
    public function __construct(
        private ReflectionClass $implementorReflectionClass,
        private WebService $webServiceAttribute,
        private SOAPBinding $SOAPBindingAttribute
    )
    {
    }

    public function getImplementorReflectionClass(): ReflectionClass
    {
        return $this->implementorReflectionClass;
    }

    public function getWebServiceAttribute(): WebService
    {
        return $this->webServiceAttribute;
    }

    public function getSOAPBindingAttribute(): SOAPBinding
    {
        return $this->SOAPBindingAttribute;
    }
}
