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
    public static function createParameterForSimpleArray()
    {
        $doc = '/**@param string[] $names*/';
        return new MethodParser('', $doc);
    }

    public static function createParameterForSimpleObject()
    {
        $doc = '/**@param object $info @string=$name @int=$age*/';
        return new MethodParser('', $doc);
    }

    public static function createParameterForObjectWithWrapper()
    {
        $doc = '/**@param object $agentNameWithId @(wrapper $agent @className=\Mocks\MockUserWrapper) @int=$id*/';
        return new MethodParser('', $doc);
    }

    public static function createParameterForObjectWithArrayOfSimpleType()
    {
        $doc = '/**@param object $namesInfo @string[]=$names @int=$id*/';
        return new MethodParser('', $doc);
    }

    public static function createParameterForArrayOfObjects()
    {
        $doc = '/**@param object[] $companies @string=$name @int=$id*/';
        return new MethodParser('', $doc);
    }

    public static function createParameterObjectWithArrayOfWrapper()
    {
        $doc = '/**@param object $listOfAgents @(wrapper[] $agents @className=\Mocks\MockUserWrapper) @int=$id*/';
        return new MethodParser('', $doc);
    }
}