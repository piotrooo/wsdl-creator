<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Annotation;

use Attribute;
use InvalidArgumentException;

/**
 * This attribute is used to specify the binding to use for a Web Service endpoint implementation class.
 *
 * @author Piotr Olaszewski
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class BindingType
{
    public function __construct(private string $value = Binding::SOAP11)
    {
        if (!in_array($value, [Binding::SOAP11, Binding::SOAP12])) {
            throw new InvalidArgumentException("Unsupported binding '{$value}'");
        }
    }

    /**
     * A binding identifier (a URI). If not specified, the default is the SOAP 1.1 / HTTP binding.<br>
     * See the {@link Binding} for definitions of standard binding interfaces.
     *
     * @return string A binding identifier (URI)
     * @see Binding
     */
    public function value(): string
    {
        return $this->value;
    }
}
