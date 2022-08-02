<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Annotation;

/**
 * Enum with the SOAP binding versions.
 *
 * @author Piotr Olaszewski
 */
final class Binding
{
    /**
     * A constant representing the identity of the SOAP 1.1 over HTTP binding.
     */
    public const SOAP11 = 'soap';

    /**
     * A constant representing the identity of the SOAP 1.2 over HTTP binding.
     */
    public const SOAP12 = 'soap12';
}
