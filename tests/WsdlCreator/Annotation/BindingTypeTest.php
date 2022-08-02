<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Annotation;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @author Piotr Olaszewski
 */
class BindingTypeTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowExceptionOnInvalidBindingType(): void
    {
        //when/then
        assertThatThrownBy(fn() => new BindingType('invalid-type'))
            ->isInstanceOf(InvalidArgumentException::class)
            ->hasMessage("Unsupported binding 'invalid-type'");
    }
}
