<?php
/**
 * RpcEncodedTest
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
use Factory\ParameterFactory;
use WSDL\XML\Styles\RpcEncoded;

class RpcEncodedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RpcEncoded
     */
    private $_rpcEncoded;

    protected function setUp()
    {
        parent::setUp();
        $this->_rpcEncoded = new RpcEncoded();
    }

    /**
     * @test
     */
    public function shouldReturnCorrectBindingStyle()
    {
        //when
        $style = $this->_rpcEncoded->bindingStyle();

        //then
        $this->assertEquals('rpc', $style);
    }

    /**
     * @test
     */
    public function shouldReturnCorrectBindingUse()
    {
        //when
        $style = $this->_rpcEncoded->bindingUse();

        //then
        $this->assertEquals('encoded', $style);
    }

    /**
     * @test
     */
    public function shouldParseArrayWithSimpleType()
    {
        //given
        $method = ParameterFactory::createParameterForSimpleArray();

        //when
        $types = $this->_rpcEncoded->typeParameters($method);

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
        $parameter = ParameterFactory::createParameterForSimpleObject();

        //when
        $types = $this->_rpcEncoded->typeParameters($parameter);

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
        $parameter = ParameterFactory::createParameterForObjectWithWrapper();

        //when
        $types = $this->_rpcEncoded->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('AgentNameWithId', $type->getName());
        $this->assertEquals(array(
            array('type' => 'element', 'value' => 'ns:MocksMockUserWrapper', 'name' => 'agent'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $type->getElementAttributes());
        $this->assertEquals('MocksMockUserWrapper', $type->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id'),
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
        ), $type->getComplex()->getElementAttributes());
    }

    /**
     * @test
     */
    public function shouldParseObjectWithArrayOfElement()
    {
        //given
        $parameter = ParameterFactory::createParameterForObjectWithArrayOfSimpleType();

        //when
        $types = $this->_rpcEncoded->typeParameters($parameter);

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
        $parameter = ParameterFactory::createParameterForArrayOfObjects();

        //when
        $types = $this->_rpcEncoded->typeParameters($parameter);

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
        $parameter = ParameterFactory::createParameterObjectWithArrayOfWrapper();

        //when
        $types = $this->_rpcEncoded->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('ListOfAgents', $type->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'ns:ArrayOfAgents', 'name' => 'agents'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $type->getElementAttributes());
        $this->assertEquals('ArrayOfAgents', $type->getComplex()->getName());
        $this->assertEquals('ns:MocksMockUserWrapper[]', $type->getComplex()->getArrayType());
        $this->assertEquals('MocksMockUserWrapper', $type->getComplex()->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id'),
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
        ), $type->getComplex()->getComplex()->getElementAttributes());
    }
}