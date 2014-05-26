<?php
/**
 * ParameterFactory
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace Factory;

use WSDL\Parser\MethodParser;

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
}
