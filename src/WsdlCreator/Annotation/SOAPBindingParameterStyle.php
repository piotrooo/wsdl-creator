<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Annotation;

/**
 * Enum with the styles of mapping parameters onto SOAP messages.
 *
 * @author Piotr Olaszewski
 */
final class SOAPBindingParameterStyle
{
    public const BARE = 'BARE';
    public const WRAPPED = 'WRAPPED';
}
