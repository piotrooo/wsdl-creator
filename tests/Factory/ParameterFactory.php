<?php
/**
 * Copyright (C) 2013-2015
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
namespace Factory;

use WSDL\Parser\MethodParser;

/**
 * ParameterFactory
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class ParameterFactory
{
    public static function createParameterForSimpleArray($methodName = '')
    {
        $doc = '/**@param string[] $names*/';
        return new MethodParser($methodName, $doc);
    }

    public static function createParameterForSimpleObject($methodName = '')
    {
        $doc = '/**@param object $info @string=$name @int=$age*/';
        return new MethodParser($methodName, $doc);
    }

    public static function createParameterForObjectWithWrapper($methodName = '')
    {
        $doc = '/**@param object $agentNameWithId @(wrapper $agent @className=\Mocks\MockUserWrapper) @int=$id*/';
        return new MethodParser($methodName, $doc);
    }

    public static function createParameterForObjectWithArrayOfSimpleType($methodName = '')
    {
        $doc = '/**@param object $namesInfo @string[]=$names @int=$id*/';
        return new MethodParser($methodName, $doc);
    }

    public static function createParameterForArrayOfObjects($methodName = '')
    {
        $doc = '/**@param object[] $companies @string=$name @int=$id*/';
        return new MethodParser($methodName, $doc);
    }

    public static function createParameterObjectWithArrayOfWrapper($methodName = '')
    {
        $doc = '/**@param object $listOfAgents @(wrapper[] $agents @className=\Mocks\MockUserWrapper) @int=$id*/';
        return new MethodParser($methodName, $doc);
    }

    public static function createReturnForSimpleArray($methodName = '')
    {
        $doc = '/**@return string[] $names*/';
        return new MethodParser($methodName, $doc);
    }

    public static function createReturnForSimpleObject($methodName = '')
    {
        $doc = '/**@return object $info @string=$name @int=$age*/';
        return new MethodParser($methodName, $doc);
    }

    public static function createReturnForObjectWithWrapper($methodName = '')
    {
        $doc = '/**@return object $agentNameWithId @(wrapper $agent @className=\Mocks\MockUserWrapper) @int=$id*/';
        return new MethodParser($methodName, $doc);
    }

    public static function createReturnForObjectWithArrayOfSimpleType($methodName = '')
    {
        $doc = '/**@return object $namesInfo @string[]=$names @int=$id*/';
        return new MethodParser($methodName, $doc);
    }

    public static function createReturnForArrayOfObjects($methodName = '')
    {
        $doc = '/**@return object[] $companies @string=$name @int=$id*/';
        return new MethodParser($methodName, $doc);
    }

    public static function createReturnObjectWithArrayOfWrapper($methodName = '')
    {
        $doc = '/**@return object $listOfAgents @(wrapper[] $agents @className=\Mocks\MockUserWrapper) @int=$id*/';
        return new MethodParser($methodName, $doc);
    }

    public static function createParameterWithMultipleWrappers($methodName = '')
    {
        $doc = '/**
        * @param wrapper $customer @className=\Mocks\WrapperClass\Customer
        * @param wrapper $purchase @className=\Mocks\WrapperClass\Purchase
        */';
        return new MethodParser($methodName, $doc);
    }
}
