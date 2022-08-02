<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Annotation;

use Attribute;

/**
 * Customizes the mapping of the return value to a WSDL part and XML element.
 *
 * @author Piotr Olaszewski
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class WebResult
{
    public function __construct(
        public ?string $name = null
    )
    {
    }
}
