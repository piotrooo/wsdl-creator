<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Internal\Model;

use ReflectionMethod;
use WsdlCreator\Annotation\WebMethod;

/**
 * @author Piotr Olaszewski
 */
class Method
{
    /**
     * @param MethodParameter[] $parameters
     */
    public function __construct(
        private ReflectionMethod $reflectionMethod,
        private ?WebMethod $webMethodAttribute,
        private array $parameters,
        private MethodReturn $return
    )
    {
    }

    public function getReflectionMethod(): ReflectionMethod
    {
        return $this->reflectionMethod;
    }

    public function getWebMethodAttribute(): ?WebMethod
    {
        return $this->webMethodAttribute;
    }

    /**
     * @return MethodParameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getReturn(): MethodReturn
    {
        return $this->return;
    }

    public function getOperationName(): string
    {
        return $this->webMethodAttribute?->operationName() ?: $this->getReflectionMethod()->getName();
    }
}
