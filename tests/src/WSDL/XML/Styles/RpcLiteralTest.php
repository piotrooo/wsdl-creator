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
use Factory\ParameterFactory;
use Ouzo\Tests\Assert;
use WSDL\XML\Styles\RpcLiteral;

/**
 * RpcLiteralTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class RpcLiteralTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RpcLiteral
     */
    private $_rpcLiteral;

    protected function setUp()
    {
        parent::setUp();
        $this->_rpcLiteral = new RpcLiteral();
    }

    /**
     * @test
     */
    public function shouldReturnCorrectBindingStyle()
    {
        //when
        $style = $this->_rpcLiteral->bindingStyle();

        //then
        $this->assertEquals('rpc', $style);
    }

    /**
     * @test
     */
    public function shouldReturnCorrectBindingUse()
    {
        //when
        $style = $this->_rpcLiteral->bindingUse();

        //then
        $this->assertEquals('literal', $style);
    }

    /**
     * @test
     */
    public function shouldParseArrayWithSimpleType()
    {
        //given
        $parameter = ParameterFactory::createParameterForSimpleArray();

        //when
        $types = $this->_rpcLiteral->typeParameters($parameter);

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
        $types = $this->_rpcLiteral->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('Info', $type->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
        ), $type->getElementAttributes());
//        $this->assertNull($type->getComplex());
    }

    /**
     * @test
     */
    public function shouldParseObjectWithWrapper()
    {
        //given
        $parameter = ParameterFactory::createParameterForObjectWithWrapper();

        //when
        $types = $this->_rpcLiteral->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('AgentNameWithId', $type->getName());
        $this->assertEquals(array(
            array('type' => 'element', 'value' => 'ns:MocksMockUserWrapper', 'name' => 'agent'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $type->getElementAttributes());
        $actualComplex = $type->getComplex();
        Assert::thatArray($actualComplex)->onMethod('getName')->containsExactly('MocksMockUserWrapper');
        Assert::thatArray($type->getComplex())->onMethod('getElementAttributes')
            ->containsKeyAndValue(array(array(
                array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id'),
                array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
                array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
            )));
    }

    /**
     * @test
     */
    public function shouldParseObjectWithArrayOfElement()
    {
        //given
        $parameter = ParameterFactory::createParameterForObjectWithArrayOfSimpleType();

        //when
        $types = $this->_rpcLiteral->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('NamesInfo', $type->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'ns:ArrayOfNames', 'name' => 'names'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $type->getElementAttributes());
        Assert::thatArray($type->getComplex())->onMethod('getName')->containsExactly('ArrayOfNames');
        Assert::thatArray($type->getComplex())->onMethod('getArrayType')->containsExactly('xsd:string[]');
    }

    /**
     * @test
     */
    public function shouldParseArrayOfObjects()
    {
        //given
        $parameter = ParameterFactory::createParameterForArrayOfObjects();

        //when
        $types = $this->_rpcLiteral->typeParameters($parameter);

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
        $types = $this->_rpcLiteral->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('ListOfAgents', $type->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'ns:ArrayOfAgents', 'name' => 'agents'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $type->getElementAttributes());
        $actualComplex = $type->getComplex();
        Assert::thatArray($actualComplex)->onMethod('getName')->containsExactly('ArrayOfAgents');
        Assert::thatArray($actualComplex)->onMethod('getArrayType')->containsExactly('ns:MocksMockUserWrapper[]');
        $this->assertEquals('MocksMockUserWrapper', $actualComplex[0]->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id'),
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
        ), $actualComplex[0]->getComplex()->getElementAttributes());
    }
}
