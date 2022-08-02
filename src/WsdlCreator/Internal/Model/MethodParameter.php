<?php

namespace WsdlCreator\Internal\Model;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use ReflectionParameter;

class MethodParameter
{
    public function __construct(
        private ReflectionParameter $reflectionParameter,
        private ?Param $param
    )
    {
    }

    public function getReflectionParameter(): ReflectionParameter
    {
        return $this->reflectionParameter;
    }

    public function getParam(): ?Param
    {
        return $this->param;
    }
}
