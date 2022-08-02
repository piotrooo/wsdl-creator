<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Internal;

use ReflectionClass;
use WsdlCreator\Annotation\Binding;
use WsdlCreator\Annotation\BindingType;

/**
 * Parsed binding id string.
 *
 * @author Piotr Olaszewski
 */
class BindingId
{
    private function __construct()
    {
    }

    public static function parse(ReflectionClass $implementorReflectionClass): string
    {
        $bindingTypeAttributes = $implementorReflectionClass->getAttributes(BindingType::class);
        if (empty($bindingTypeAttributes)) {
            return Binding::SOAP11;
        }

        /** @var BindingType $bindingType */
        $bindingType = $bindingTypeAttributes[0]->newInstance();
        return $bindingType->value();
    }
}
