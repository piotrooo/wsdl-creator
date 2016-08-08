<?php
/**
 * Copyright (C) 2013-2016
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

use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use WSDL\Annotation\BindingType;
use WSDL\Annotation\SoapBinding;
use WSDL\Builder\IsValid;

/**
 * IsValidTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class IsValidTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider validStyles
     * @param string $style
     */
    public function shouldNotThrowExceptionWhenStyleIsValid($style)
    {
        //when
        IsValid::style($style);

        //then no exception
    }

    public function validStyles()
    {
        return [
            [SoapBinding::RPC],
            [SoapBinding::DOCUMENT]
        ];
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenStyleIsInValid()
    {
        //when
        try {
            IsValid::style('INVALID_STYLE');
            $this->assertFalse(true, 'Triggered when exception is not throw');
        } catch (InvalidArgumentException $e) {
            //then
            $this->assertEquals('Invalid style [INVALID_STYLE] available styles: [RPC, DOCUMENT]', $e->getMessage());
            $this->assertInstanceOf('\InvalidArgumentException', $e);
        }
    }

    /**
     * @test
     * @dataProvider validUses
     * @param string $use
     */
    public function shouldNotThrowExceptionWhenUseIsValid($use)
    {
        //when
        IsValid::useStyle($use);

        //then no exception
    }

    public function validUses()
    {
        return [
            [SoapBinding::LITERAL],
            [SoapBinding::ENCODED]
        ];
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenUseIsInValid()
    {
        //when
        try {
            IsValid::useStyle('INVALID_USE');
            $this->assertFalse(true, 'Triggered when exception is not throw');
        } catch (InvalidArgumentException $e) {
            //then
            $this->assertEquals('Invalid use [INVALID_USE] available uses: [LITERAL, ENCODED]', $e->getMessage());
            $this->assertInstanceOf('\InvalidArgumentException', $e);
        }
    }

    /**
     * @test
     * @dataProvider validSoapVersions
     * @param string $soapVersion
     */
    public function shouldNotThrowExceptionWhenSoapVersionIsValid($soapVersion)
    {
        //when
        IsValid::soapVersion($soapVersion);

        //then no exception
    }

    public function validSoapVersions()
    {
        return [
            [BindingType::SOAP_11],
            [BindingType::SOAP_12]
        ];
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenSoapVersionIsInValid()
    {
        //when
        try {
            IsValid::soapVersion('INVALID_SOAP_VERSION');
            $this->assertFalse(true, 'Triggered when exception is not throw');
        } catch (InvalidArgumentException $e) {
            //then
            $this->assertEquals('Invalid binding type [INVALID_SOAP_VERSION] available types: [SOAP_11, SOAP_12]', $e->getMessage());
            $this->assertInstanceOf('\InvalidArgumentException', $e);
        }
    }

    /**
     * @test
     */
    public function shouldNotThrowExceptionWhenValueIsNotEmpty()
    {
        //when
        IsValid::notEmpty('some value');

        //then no exception
    }

    /**
     * @test
     * @dataProvider emptyValues
     * @param mixed $value
     */
    public function shouldThrowExceptionWhenValueIsEmpty($value)
    {
        //when
        try {
            IsValid::notEmpty($value);
            $this->assertFalse(true, 'Triggered when exception is not throw');
        } catch (InvalidArgumentException $e) {
            //then
            $this->assertEquals('Value cannot be empty', $e->getMessage());
            $this->assertInstanceOf('\InvalidArgumentException', $e);
        }
    }

    /**
     * @test
     * @dataProvider emptyValues
     * @param mixed $value
     * @param string $customMessage
     */
    public function shouldThrowExceptionWithCustomMessageWhenValueIsEmpty($value, $customMessage)
    {
        //when
        try {
            IsValid::notEmpty($value, $customMessage);
            $this->assertFalse(true, 'Triggered when exception is not throw');
        } catch (InvalidArgumentException $e) {
            //then
            $this->assertEquals($customMessage, $e->getMessage());
            $this->assertInstanceOf('\InvalidArgumentException', $e);
        }
    }

    public function emptyValues()
    {
        return [
            ['', 'First empty value'],
            [null, 'Second empty value']
        ];
    }
}
