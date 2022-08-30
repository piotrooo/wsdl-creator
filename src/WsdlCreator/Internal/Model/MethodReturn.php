<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Internal\Model;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use ReflectionNamedType;

/**
 * @author Piotr Olaszewski
 */
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
