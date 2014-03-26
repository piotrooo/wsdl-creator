<?php
use WSDL\Parser\ComplexTypeParser;
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

    /**
     * @test
     */
    public function shouldParseObjectWithWrapper()
    {
        //given
        $complexType = new Object('Agent', 'agent', array(
            new ComplexTypeParser('string', 'name'),
            new ComplexTypeParser('int', 'number')
        ));
        $complex = array(new Object('Agent', 'agent', $complexType));
        $parameter = array(new Object('object', 'agentNameWithId', $complex));
        $rpcLiteral = new RpcLiteral();

        //when
        $types = $rpcLiteral->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('AgentNameWithId', $type->getName());
        $this->assertEquals(array(
            array('type' => 'element', 'value' => 'ns:Agent', 'name' => 'agent')
        ), $type->getElementAttributes());
        $this->assertEquals('Agent', $type->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'number')
        ), $type->getComplex()->getElementAttributes());
    }

    /**
     * @test
     */
    public function shouldParseObjectWithArrayOfElement()
    {
        //given
        $complex = array(new Arrays('string', 'names', null), new Simple('int', 'id'));
        $parameter = array(new Object('object', 'namesInfo', $complex));
        $rpcLiteral = new RpcLiteral();

        //when
        $types = $rpcLiteral->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('NamesInfo', $type->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'ns:ArrayOfNames', 'name' => 'names'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $type->getElementAttributes());
        $this->assertEquals('ArrayOfNames', $type->getComplex()->getName());
        $this->assertEquals('xsd:string[]', $type->getComplex()->getArrayType());
    }

    /**
     * @test
     */
    public function shouldParseArrayOfObjects()
    {
        //given
        $complexType = array(new Simple('string', 'name'), new Simple('int', 'id'));
        $complex = new Object('object', 'companies', $complexType);
        $parameter = array(new Arrays('object', 'companies', $complex));
        $rpcLiteral = new RpcLiteral();

        //when
        $types = $rpcLiteral->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('ArrayOfCompanies', $type->getName());
        $this->assertEquals('ns:Companies[]', $type->getArrayType());
        $this->assertEquals('Companies', $type->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $type->getComplex()->getElementAttributes());
    }

    /**
     * @test
     */
    public function shouldParseObjectWithArrayOfWrapper()
    {
        //given
        $complexType = new Object('Agent', 'agents', array(
            new ComplexTypeParser('string', 'name'), new ComplexTypeParser('int', 'number')
        ));
        $complex = array(new Arrays('Agent', 'agents', $complexType), new Simple('int', 'id'));
        $parameter = array(new Object('object', 'listOfAgents', $complex));
        $rpcLiteral = new RpcLiteral();

        //when
        $types = $rpcLiteral->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('ListOfAgents', $type->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'ns:ArrayOfAgents', 'name' => 'agents'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $type->getElementAttributes());
        $this->assertEquals('ArrayOfAgents', $type->getComplex()->getName());
        $this->assertEquals('ns:Agent[]', $type->getComplex()->getArrayType());
        $this->assertEquals('Agent', $type->getComplex()->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'number')
        ), $type->getComplex()->getComplex()->getElementAttributes());
    }
}