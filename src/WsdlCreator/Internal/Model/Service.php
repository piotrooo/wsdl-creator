<?php

namespace WsdlCreator\Internal\Model;

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
