<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Annotation;

use Attribute;

/**
 * Customizes a method that is exposed as a Web Service operation.
 *
 * @author Piotr Olaszewski
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class WebMethod
{
    public function __construct(
        private ?string $operationName = null,
        private ?string $action = null,
        private bool $exclude = false
    )
    {
    }

    /**
     * Name of the <code>wsdl:operation</code> matching this method.
     *
     * @return string|null the name of the <code>wsdl:operation</code>
     */
    public function operationName(): ?string
    {
        return $this->operationName;
    }

    /**
     * The action of this operation.<br>
     * For SOAP bindings, that determines the value of the SOAP action.
     *
     * @return string|null the action
     */
    public function action(): ?string
    {
        return $this->action;
    }

    /**
     * Marks a method to NOT be exposed as a web method.
     *
     * @return bool value of <code>true</code> to mark the method to not be exposed or <code>false</code> otherwise
     */
    public function exclude(): bool
    {
        return $this->exclude;
    }
}
