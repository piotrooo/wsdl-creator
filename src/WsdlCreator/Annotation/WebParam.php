<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Annotation;

use Attribute;

/**
 * Customizes the mapping of an individual parameter to a Web Service message part and XML element.
 *
 * @author Piotr Olaszewski
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
final class WebParam
{
    public function __construct(
        private ?string $name = null,
        private ?string $partName = null,
        private bool $header = false
    )
    {
    }

    /**
     * Name of the parameter.
     *
     * @return string|null the name of the parameter
     */
    public function name(): ?string
    {
        return $this->name;
    }

    /**
     * The name of the <code>wsdl:part</code> representing this parameter.
     * This is only used if the operation is RPC style or if the operation is DOCUMENT style and the parameter style
     * is BARE.
     *
     * @return string|null the name of the <code>wsdl:part</code>
     */
    public function partName(): ?string
    {
        return $this->partName;
    }

    /**
     * If true, the parameter is pulled from a message header rather then the message body.
     *
     * @return bool value of <code>true</code> to pull the parameter from a message header rather then the message body
     * or <code>false</code> otherwise
     */
    public function header(): bool
    {
        return $this->header;
    }
}
