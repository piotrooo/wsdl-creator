<?php
/**
 * ParameterFactory
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace Factory;

use WSDL\Parser\ComplexTypeParser;
use WSDL\Types\Arrays;
use WSDL\Types\Object;
use WSDL\Types\Simple;

class ParameterFactory
{
    public static function createParameterForSimpleArray()
    {
        //@param string[] $names
        return array(new Arrays('string', 'names', null));
    }

    public static function createParameterForSimpleObject()
    {
        //@param object $info @string=$name @int=$age
        $complex = array(new Simple('string', 'name'), new Simple('int', 'age'));
        return array(new Object('object', 'info', $complex));
    }

    public static function createParameterForObjectWithWrapper()
    {
        //@param object $agentNameWithId @(wrapper $agent @className=Agent) @int=$id
        $complexType = new Object('Agent', 'agent', array(
            new ComplexTypeParser('string', 'name'),
            new ComplexTypeParser('int', 'number')
        ));
        $complex = array(new Object('Agent', 'agent', $complexType));
        return array(new Object('object', 'agentNameWithId', $complex));
    }

    public static function createParameterForObjectWithArrayOfSimpleType()
    {
        //@param object $namesInfo @string[]=$names @int=$id
        $complex = array(new Arrays('string', 'names', null), new Simple('int', 'id'));
        return array(new Object('object', 'namesInfo', $complex));
    }

    public static function createParameterForArrayOfObjects()
    {
        //@param object[] $companies @string=$name @int=$id
        $complexType = array(new Simple('string', 'name'), new Simple('int', 'id'));
        $complex = new Object('object', 'companies', $complexType);
        return array(new Arrays('object', 'companies', $complex));
    }

    public static function createParameterObjectWithArrayOfWrapper()
    {
        //@param object $listOfAgents @(wrapper[] $agents @className=Agent) @int=$id
        $complexType = new Object('Agent', 'agents', array(
            new ComplexTypeParser('string', 'name'), new ComplexTypeParser('int', 'number')
        ));
        $complex = array(new Arrays('Agent', 'agents', $complexType), new Simple('int', 'id'));
        return array(new Object('object', 'listOfAgents', $complex));
    }
}