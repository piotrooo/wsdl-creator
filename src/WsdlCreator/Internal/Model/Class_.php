<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Internal\Model;

use ReflectionClass;
use WsdlCreator\Annotation\SOAPBinding;
use WsdlCreator\Annotation\WebService;

/**
 * @author Piotr Olaszewski
 */
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

    public function getName(): string
    {
        return $this->webServiceAttribute->name() ?: $this->getShortName();
    }

    public function getTargetNamespace(): string
    {
        $targetNamespace = $this->getWebServiceAttribute()->targetNamespace();
        if (is_null($targetNamespace)) {
            $fullName = strtolower($this->implementorReflectionClass->getName());
            $targetNamespace = collect(explode('\\', $fullName))
                ->reverse()
                ->implode('.');
        }

        if (!filter_var($targetNamespace, FILTER_VALIDATE_URL)) {
            $targetNamespace = "urn:{$targetNamespace}";
        }

        return $targetNamespace;
    }

    public function getServiceName(): string
    {
        return $this->webServiceAttribute->serviceName() ?: "{$this->getShortName()}Service";
    }

    public function getPortName(): string
    {
        return $this->webServiceAttribute->portName() ?: "{$this->getShortName()}Port";
    }

    private function getShortName(): string
    {
        return $this->implementorReflectionClass->getShortName();
    }
}
