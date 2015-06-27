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
use WSDL\XML\Styles\DocumentLiteralWrapped;

/**
 * DocumentLiteralWrappedTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class DocumentLiteralWrappedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DocumentLiteralWrapped
     */
    private $_documentLiteralWrapped;

    protected function setUp()
    {
        parent::setUp();
        $this->_documentLiteralWrapped = new DocumentLiteralWrapped();
    }

    /**
     * @test
     */
    public function shouldReturnCorrectBindingStyle()
    {
        //when
        $style = $this->_documentLiteralWrapped->bindingStyle();

        //then
        $this->assertEquals('document', $style);
    }

    /**
     * @test
     */
    public function shouldReturnCorrectBindingUse()
    {
        //when
        $style = $this->_documentLiteralWrapped->bindingUse();

        //then
        $this->assertEquals('literal', $style);
    }

    /**
     * @test
     */
    public function shouldParseArrayWithSimpleType()
    {
        //given
        $method = ParameterFactory::createParameterForSimpleArray('method');

        //when
        $types = $this->_documentLiteralWrapped->typeParameters($method);

        //then
        $type = $types[0];
        // convention 4, input wrapper element name should match with Operation name
        $this->assertEquals('method', $type->getName());
        $this->assertEquals(array(array('type' => 'type', 'value' => 'ns:ArrayOfNames', 'name' => 'names')), $type->getElementAttributes());
        Assert::thatArray($type->getComplex())->onMethod('getName')->containsExactly('ArrayOfNames');
        Assert::thatArray($type->getComplex())->onMethod('getArrayType')->containsExactly('xsd:string[]');
    }

    /**
     * @test
     */
    public function shouldParseSimpleObject()
    {
        //given
        $parameter = ParameterFactory::createParameterForSimpleObject('method');

        //when
        $types = $this->_documentLiteralWrapped->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('method', $type->getName());
        $this->assertEquals(array(array('type' => 'element', 'value' => 'ns:Info', 'name' => 'info')), $type->getElementAttributes());
        Assert::thatArray($type->getComplex())->onMethod('getName')->containsExactly('Info');
        Assert::thatArray($type->getComplex())->onMethod('getElementAttributes')
            ->containsKeyAndValue(array(array(
                array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
                array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
            )));
    }

    /**
     * @test
     */
    public function shouldParseObjectWithWrapper()
    {
        //given
        $parameter = ParameterFactory::createParameterForObjectWithWrapper('method');

        //when
        $types = $this->_documentLiteralWrapped->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('method', $type->getName());
        $this->assertEquals(array(
            array('type' => 'element', 'value' => 'ns:AgentNameWithId', 'name' => 'agentNameWithId')
        ), $type->getElementAttributes());
        $complexActual = $type->getComplex();
        Assert::thatArray($complexActual)->onMethod('getName')->containsExactly('AgentNameWithId');
        Assert::thatArray($complexActual)->onMethod('getElementAttributes')
            ->containsKeyAndValue(array(array(
                array('type' => 'element', 'value' => 'ns:MocksMockUserWrapper', 'name' => 'agent'),
                array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
            )));
        Assert::thatArray($complexActual[0]->getComplex())->onMethod('getElementAttributes')
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
        $parameter = ParameterFactory::createParameterForObjectWithArrayOfSimpleType('method');

        //when
        $types = $this->_documentLiteralWrapped->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('method', $type->getName());
        $this->assertEquals(array(
            array('type' => 'element', 'value' => 'ns:NamesInfo', 'name' => 'namesInfo')
        ), $type->getElementAttributes());
        $actualContext = $type->getComplex();
        Assert::thatArray($actualContext)->onMethod('getName')->containsExactly('NamesInfo');
        Assert::thatArray($actualContext)->onMethod('getElementAttributes')
            ->containsKeyAndValue(array(array(
                array('type' => 'type', 'value' => 'ns:ArrayOfNames', 'name' => 'names'),
                array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
            )));
        Assert::thatArray($actualContext[0]->getComplex())->onMethod('getName')->containsExactly('ArrayOfNames');
        Assert::thatArray($actualContext[0]->getComplex())->onMethod('getArrayType')->containsExactly('xsd:string[]');
    }

    /**
     * @test
     */
    public function shouldParseArrayOfObjects()
    {
        //given
        $parameter = ParameterFactory::createParameterForArrayOfObjects('method');

        //when
        $types = $this->_documentLiteralWrapped->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('method', $type->getName());
        $this->assertEquals(array(array('type' => 'type', 'value' => 'ns:ArrayOfCompanies', 'name' => 'companies')), $type->getElementAttributes());
        $actualContext = $type->getComplex();
        Assert::thatArray($actualContext)->onMethod('getName')->containsExactly('ArrayOfCompanies');
        Assert::thatArray($actualContext)->onMethod('getArrayType')->containsExactly('ns:Companies[]');
        $this->assertEquals('Companies', $actualContext[0]->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $actualContext[0]->getComplex()->getElementAttributes());
    }

    /**
     * @test
     */
    public function shouldParseObjectWithArrayOfWrapper()
    {
        //given
        $parameter = ParameterFactory::createParameterObjectWithArrayOfWrapper('method');

        //when
        $types = $this->_documentLiteralWrapped->typeParameters($parameter);

        //then
        $type = $types[0];
        $this->assertEquals('method', $type->getName());
        $this->assertEquals(array(array('type' => 'element', 'value' => 'ns:ListOfAgents', 'name' => 'listOfAgents')), $type->getElementAttributes());
        $actualContext = $type->getComplex();
        Assert::thatArray($actualContext)->onMethod('getName')->containsExactly('ListOfAgents');
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'ns:ArrayOfAgents', 'name' => 'agents'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $actualContext[0]->getElementAttributes());
        Assert::thatArray($actualContext[0]->getComplex())->onMethod('getName')->containsExactly('ArrayOfAgents');
        Assert::thatArray($actualContext[0]->getComplex())->onMethod('getArrayType')->containsExactly('ns:MocksMockUserWrapper[]');
        $actualComplex2 = $actualContext[0]->getComplex();
        $this->assertEquals('MocksMockUserWrapper', $actualComplex2[0]->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id'),
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
        ), $actualComplex2[0]->getComplex()->getElementAttributes());
    }

    /**
     * @test
     */
    public function shouldParseReturnArrayWithSimpleType()
    {
        //given
        $method = ParameterFactory::createReturnForSimpleArray('method');

        //when
        $element = $this->_documentLiteralWrapped->typeReturning($method);

        //then
        // convention 4, input wrapper element name should match with Operation name
        $this->assertEquals('methodResponse', $element->getName());
        $this->assertEquals(array(array('type' => 'type', 'value' => 'ns:ArrayOfNames', 'name' => 'names')), $element->getElementAttributes());
        Assert::thatArray($element->getComplex())->onMethod('getName')->containsExactly('ArrayOfNames');
        Assert::thatArray($element->getComplex())->onMethod('getArrayType')->containsExactly('xsd:string[]');
    }

    /**
     * @test
     */
    public function shouldParseReturnSimpleObject()
    {
        //given
        $parameter = ParameterFactory::createReturnForSimpleObject('method');

        //when
        $element = $this->_documentLiteralWrapped->typeReturning($parameter);

        //then
        $this->assertEquals('methodResponse', $element->getName());
        $this->assertEquals(array(array('type' => 'element', 'value' => 'ns:Info', 'name' => 'info')), $element->getElementAttributes());
        Assert::thatArray($element->getComplex())->onMethod('getName')->containsExactly('Info');
        Assert::thatArray($element->getComplex())->onMethod('getElementAttributes')
            ->containsKeyAndValue(array(array(
                array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
                array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
            )));
    }

    /**
     * @test
     */
    public function shouldParseReturnObjectWithWrapper()
    {
        //given
        $parameter = ParameterFactory::createReturnForObjectWithWrapper('method');

        //when
        $element = $this->_documentLiteralWrapped->typeReturning($parameter);

        //then
        $this->assertEquals('methodResponse', $element->getName());
        $this->assertEquals(array(
            array('type' => 'element', 'value' => 'ns:AgentNameWithId', 'name' => 'agentNameWithId')
        ), $element->getElementAttributes());
        $actualComplex = $element->getComplex();
        Assert::thatArray($actualComplex)->onMethod('getName')->containsExactly('AgentNameWithId');
        Assert::thatArray($actualComplex)->onMethod('getElementAttributes')
            ->containsKeyAndValue(array(array(
                array('type' => 'element', 'value' => 'ns:MocksMockUserWrapper', 'name' => 'agent'),
                array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
            )));
        $actualComplex2 = $actualComplex[0]->getComplex();
        Assert::thatArray($actualComplex2)->onMethod('getElementAttributes')
            ->containsKeyAndValue(array(array(
                array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id'),
                array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
                array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
            )));
    }

    /**
     * @test
     */
    public function shouldParseReturnObjectWithArrayOfElement()
    {
        //given
        $parameter = ParameterFactory::createReturnForObjectWithArrayOfSimpleType('method');

        //when
        $element = $this->_documentLiteralWrapped->typeReturning($parameter);

        //then
        $this->assertEquals('methodResponse', $element->getName());
        $this->assertEquals(array(
            array('type' => 'element', 'value' => 'ns:NamesInfo', 'name' => 'namesInfo')
        ), $element->getElementAttributes());

        $actualComplex = $element->getComplex();
        Assert::thatArray($actualComplex)->onMethod('getName')->containsExactly('NamesInfo');
        Assert::thatArray($actualComplex)->onMethod('getElementAttributes')
            ->containsKeyAndValue(array(array(
                array('type' => 'type', 'value' => 'ns:ArrayOfNames', 'name' => 'names'),
                array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
            )));
        Assert::thatArray($actualComplex[0]->getComplex())->onMethod('getName')->containsExactly('ArrayOfNames');
        Assert::thatArray($actualComplex[0]->getComplex())->onMethod('getArrayType')->containsExactly('xsd:string[]');
    }

    /**
     * @test
     */
    public function shouldParseReturnArrayOfObjects()
    {
        //given
        $parameter = ParameterFactory::createReturnForArrayOfObjects('method');

        //when
        $element = $this->_documentLiteralWrapped->typeReturning($parameter);

        //then
        $this->assertEquals('methodResponse', $element->getName());
        $this->assertEquals(array(array('type' => 'type', 'value' => 'ns:ArrayOfCompanies', 'name' => 'companies')), $element->getElementAttributes());
        $actualComplex = $element->getComplex();
        Assert::thatArray($actualComplex)->onMethod('getName')->containsExactly('ArrayOfCompanies');
        Assert::thatArray($actualComplex)->onMethod('getArrayType')->containsExactly('ns:Companies[]');
        $this->assertEquals('Companies', $actualComplex[0]->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $actualComplex[0]->getComplex()->getElementAttributes());
    }

    /**
     * @test
     */
    public function shouldParseReturnObjectWithArrayOfWrapper()
    {
        //given
        $parameter = ParameterFactory::createReturnObjectWithArrayOfWrapper('method');

        //when
        $element = $this->_documentLiteralWrapped->typeReturning($parameter);

        //then
        $this->assertEquals('methodResponse', $element->getName());
        $this->assertEquals(array(array('type' => 'element', 'value' => 'ns:ListOfAgents', 'name' => 'listOfAgents')), $element->getElementAttributes());
        $actualComplex = $element->getComplex();
        Assert::thatArray($actualComplex)->onMethod('getName')->containsExactly('ListOfAgents');
        Assert::thatArray($actualComplex)->onMethod('getElementAttributes')
            ->containsKeyAndValue(array(array(
                array('type' => 'type', 'value' => 'ns:ArrayOfAgents', 'name' => 'agents'),
                array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
            )));
        $actualComplex2 = $actualComplex[0]->getComplex();
        $this->assertEquals('ArrayOfAgents', $actualComplex2[0]->getName());
        $this->assertEquals('ns:MocksMockUserWrapper[]', $actualComplex2[0]->getArrayType());
        $this->assertEquals('MocksMockUserWrapper', $actualComplex2[0]->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id'),
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
        ), $actualComplex2[0]->getComplex()->getElementAttributes());
    }

    /**
     * @test
     */
    public function shouldParseWhenMultipleWrappers()
    {
        //given
        $parameter = ParameterFactory::createParameterWithMultipleWrappers('method');

        //when
        $types = $this->_documentLiteralWrapped->typeParameters($parameter);

        //then
        $element = $types[0];
        Assert::thatArray($element->getElementAttributes())->containsKeyAndValue(array(
            array('type' => 'element', 'value' => 'ns:MocksWrapperClassCustomer', 'name' => 'customer'),
            array('type' => 'element', 'value' => 'ns:MocksWrapperClassPurchase', 'name' => 'purchase')
        ));
        Assert::thatArray($element->getComplex())->onMethod('getName')->containsOnly("MocksWrapperClassCustomer", "MocksWrapperClassPurchase");
    }
}
