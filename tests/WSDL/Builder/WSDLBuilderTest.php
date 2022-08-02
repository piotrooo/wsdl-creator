<?php
/**
 * Copyright (C) 2013-2022
 * Piotr Olaszewski <piotroo89@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Tests\WSDL\Builder;

use Ouzo\Tests\CatchException;
use PHPUnit\Framework\TestCase;
use WSDL\Builder\WSDLBuilder;

/**
 * WSDLBuilderTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class WSDLBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowExceptionWhenNameIsEmpty()
    {
        //given
        $WSDLBuilder = WSDLBuilder::instance();

        //when
        CatchException::when($WSDLBuilder)->setName('');

        //then
        CatchException::assertThat()
            ->hasMessage('Name cannot be empty');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenTargetNamespaceIsEmpty()
    {
        //given
        $WSDLBuilder = WSDLBuilder::instance();

        //when
        CatchException::when($WSDLBuilder)->setTargetNamespace('');

        //then
        CatchException::assertThat()
            ->hasMessage('Target namespace cannot be empty');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenNSIsEmpty()
    {
        //given
        $WSDLBuilder = WSDLBuilder::instance();

        //when
        CatchException::when($WSDLBuilder)->setNs('');

        //then
        CatchException::assertThat()
            ->hasMessage('NS cannot be empty');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenLocationIsEmpty()
    {
        //given
        $WSDLBuilder = WSDLBuilder::instance();

        //when
        CatchException::when($WSDLBuilder)->setLocation('');

        //then
        CatchException::assertThat()
            ->hasMessage('Location cannot be empty');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenStyleIsInValid()
    {
        //given
        $WSDLBuilder = WSDLBuilder::instance();

        //when
        CatchException::when($WSDLBuilder)->setStyle('INVALID');

        //then
        CatchException::assertThat()
            ->hasMessage('Invalid style [INVALID] available styles: [RPC, DOCUMENT]');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenUseIsInValid()
    {
        //given
        $WSDLBuilder = WSDLBuilder::instance();

        //when
        CatchException::when($WSDLBuilder)->setUse('INVALID');

        //then
        CatchException::assertThat()
            ->hasMessage('Invalid use [INVALID] available uses: [LITERAL, ENCODED]');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenBindingTypeIsInValid()
    {
        //given
        $WSDLBuilder = WSDLBuilder::instance();

        //when
        CatchException::when($WSDLBuilder)->setSoapVersion('INVALID');

        //then
        CatchException::assertThat()
            ->hasMessage('Invalid binding type [INVALID] available types: [SOAP_11, SOAP_12]');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenParameterStyleIsInValid()
    {
        //given
        $WSDLBuilder = WSDLBuilder::instance();

        //when
        CatchException::when($WSDLBuilder)->setParameterStyle('INVALID');

        //then
        CatchException::assertThat()
            ->hasMessage('Invalid parameter style [INVALID] available parameter styles: [BARE, WRAPPED]');
    }
}
