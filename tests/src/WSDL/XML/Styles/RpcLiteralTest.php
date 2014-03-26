<?php
use WSDL\Types\Arrays;
use WSDL\Types\Object;
use WSDL\Types\Simple;
use WSDL\XML\Styles\RpcLiteral;

class RpcLiteralTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldParseArrayWithSimpleType()
    {
        //given
        $parameter = array(new Arrays('string', 'names', null));
        $rpcLiteral = new RpcLiteral();

        //when
        $types = $rpcLiteral->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('ArrayOfNames', $type->getName());
        $this->assertEquals('xsd:string[]', $type->getArrayType());
        $this->assertNull($type->getComplex());
    }

    /**
     * @test
     */
    public function shouldParseSimpleObject()
    {
        //given
        $complex = array(new Simple('string', 'name'), new Simple('int', 'age'));
        $parameter = array(new Object('object', 'info', $complex));
        $rpcLiteral = new RpcLiteral();

        //when
        $types = $rpcLiteral->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('Info', $type->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
        ), $type->getElementAttributes());
        $this->assertNull($type->getComplex());
    }
}