<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Annotation;

use Attribute;

/**
 * Marks a PHP class as implementing a Web Service.
 *
 * @author Piotr Olaszewski
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class WebService
{
    public function __construct(
        private ?string $name = null,
        private ?string $targetNamespace = null,
        private ?string $serviceName = null,
        private ?string $portName = null
    )
    {
    }

    /**
     * The name of Web Service. Used as the name of the <code>wsdl:portType</code>.
     *
     * @return string|null the name of the Web Service
     */
    public function name(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function targetNamespace(): ?string
    {
        return $this->targetNamespace;
    }

    /**
     * The service name of the Web Service. Used as the name of the <code>wsdl:service</code>.
     *
     * @return string|null the service name
     */
    public function serviceName(): ?string
    {
        return $this->serviceName;
    }

    /**
     * The port name of the Web Service. Used as the name of the <code>wsdl:port</code>.
     *
     * @return string|null the port name
     */
    public function portName(): ?string
    {
        return $this->portName;
    }
}
