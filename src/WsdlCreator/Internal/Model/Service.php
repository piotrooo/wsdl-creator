<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Internal\Model;

/**
 * @author Piotr Olaszewski
 */
class Service
{
    /**
     * @param Method[] $methods
     */
    public function __construct(
        private Class_ $class,
        private array $methods
    )
    {
    }

    public function getClass(): Class_
    {
        return $this->class;
    }

    /**
     * @return Method[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }
}
