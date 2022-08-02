<?php

namespace WsdlCreator\Internal\Model;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use ReflectionNamedType;

class MethodReturn
{
    public function __construct(
        private ReflectionNamedType $reflectionType,
        private ?Return_ $return
    )
    {
    }

    public function getReflectionType(): ReflectionNamedType
    {
        return $this->reflectionType;
    }

    public function getReturn(): ?Return_
    {
        return $this->return;
    }
}
