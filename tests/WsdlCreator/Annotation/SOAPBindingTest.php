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
class SOAPBindingTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowExceptionOnInvalidStyle(): void
    {
        //when/then
        assertThatThrownBy(fn() => new SOAPBinding(style: 'invalid-style'))
            ->isInstanceOf(InvalidArgumentException::class)
            ->hasMessage("Unsupported style 'invalid-style'");
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnInvalidUse(): void
    {
        //when/then
        assertThatThrownBy(fn() => new SOAPBinding(use: 'invalid-use'))
            ->isInstanceOf(InvalidArgumentException::class)
            ->hasMessage("Unsupported use 'invalid-use'");
    }

    /**
     * @test
     */
    public function shouldThrowExceptionOnInvalidParameterStyle(): void
    {
        //when/then
        assertThatThrownBy(fn() => new SOAPBinding(parameterStyle: 'invalid-parameterStyle'))
            ->isInstanceOf(InvalidArgumentException::class)
            ->hasMessage("Unsupported parameter style 'invalid-parameterStyle'");
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenRPCStyleHasBAREParameterStyle(): void
    {
        //when/then
        assertThatThrownBy(fn() => new SOAPBinding(style: SOAPBindingStyle::RPC, parameterStyle: SOAPBindingParameterStyle::BARE))
            ->isInstanceOf(InvalidArgumentException::class)
            ->hasMessage('Incorrect usage of attribute, parameterStyle can only be WRAPPED with RPC style Web service');
    }
}
