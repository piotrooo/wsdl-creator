<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Internal\Model;

use phpDocumentor\Reflection\DocBlock\Tags\Param;
use ReflectionParameter;
use WsdlCreator\Annotation\SOAPBindingStyle;
use WsdlCreator\Annotation\WebParam;

/**
 * @author Piotr Olaszewski
 */
class MethodParameter
{
    public function __construct(
        private ReflectionParameter $reflectionParameter,
        private ?WebParam $webParamAttribute,
        private ?Param $param
    )
    {
    }

    public function getReflectionParameter(): ReflectionParameter
    {
        return $this->reflectionParameter;
    }

    public function getWebParamAttribute(): ?WebParam
    {
        return $this->webParamAttribute;
    }

    public function getParam(): ?Param
    {
        return $this->param;
    }

    public function getName(int $index, string $style = SOAPBindingStyle::DOCUMENT): string
    {
        if ($style === SOAPBindingStyle::RPC) {
            $partName = $this->webParamAttribute?->partName();
            if (!is_null($partName)) {
                return $partName;
            }
        }
        return $this->webParamAttribute?->name() ?: "arg{$index}";
    }
}
