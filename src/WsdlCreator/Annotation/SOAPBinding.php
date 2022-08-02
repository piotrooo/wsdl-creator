<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Annotation;

use Attribute;
use InvalidArgumentException;

/**
 * Specifies the mapping of the Web Service onto the SOAP message protocol.
 *
 * @author Piotr Olaszewski
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class SOAPBinding
{
    public function __construct(
        private string $style = SOAPBindingStyle::DOCUMENT,
        private string $use = SOAPBindingUse::LITERAL,
        private string $parameterStyle = SOAPBindingParameterStyle::WRAPPED
    )
    {
        if (!in_array($style, [SOAPBindingStyle::DOCUMENT, SOAPBindingStyle::RPC])) {
            throw new InvalidArgumentException("Unsupported style '{$style}'");
        }

        if (!in_array($use, [SOAPBindingUse::ENCODED, SOAPBindingUse::LITERAL])) {
            throw new InvalidArgumentException("Unsupported use '{$use}'");
        }

        if (!in_array($parameterStyle, [SOAPBindingParameterStyle::BARE, SOAPBindingParameterStyle::WRAPPED])) {
            throw new InvalidArgumentException("Unsupported parameter style '{$parameterStyle}'");
        }

        if ($style === SOAPBindingStyle::RPC && $parameterStyle === SOAPBindingParameterStyle::BARE) {
            throw new InvalidArgumentException('Incorrect usage of attribute, parameterStyle can only be WRAPPED with RPC style Web service');
        }
    }

    /**
     * Defines the encoding style for messages send to and from the Web Service.
     *
     * @return string the encoding style for messages
     * @see SOAPBindingStyle
     */
    public function style(): string
    {
        return $this->style;
    }

    /**
     * Defines the formatting style for messages sent to and from the Web Service.
     *
     * @return string the formatting style for messages
     * @see SOAPBindingUse
     */
    public function use(): string
    {
        return $this->use;
    }

    /**
     * Determines whether method parameters represent the entire message body, or whether the parameters are elements
     * wrapped inside a top-level element named after the operation.
     *
     * @return string parameter style as <code>BARE</code> or <code>WRAPPED</code>
     * @see SOAPBindingParameterStyle
     */
    public function parameterStyle(): string
    {
        return $this->parameterStyle;
    }
}
