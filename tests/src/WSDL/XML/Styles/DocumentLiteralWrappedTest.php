<?php
/**
 * DocumentLiteralWrappedTest
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
use Factory\ParameterFactory;
use WSDL\XML\Styles\DocumentLiteralWrapped;

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
        $this->assertEquals('ArrayOfNames', $type->getComplex()->getName());
        $this->assertEquals('xsd:string[]', $type->getComplex()->getArrayType());
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
        $this->assertEquals('Info', $type->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
        ), $type->getComplex()->getElementAttributes());
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
        $this->assertEquals('AgentNameWithId', $type->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'element', 'value' => 'ns:MocksMockUserWrapper', 'name' => 'agent'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $type->getComplex()->getElementAttributes());
        $this->assertEquals('AgentNameWithId', $type->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id'),
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
        ), $type->getComplex()->getComplex()->getElementAttributes());
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
        $this->assertEquals('NamesInfo', $type->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'ns:ArrayOfNames', 'name' => 'names'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $type->getComplex()->getElementAttributes());
        $this->assertEquals('NamesInfo', $type->getComplex()->getName());
        $this->assertEquals('ArrayOfNames', $type->getComplex()->getComplex()->getName());
        $this->assertEquals('xsd:string[]', $type->getComplex()->getComplex()->getArrayType());
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
        $this->assertEquals('ArrayOfCompanies', $type->getComplex()->getName());
        $this->assertEquals('ns:Companies[]', $type->getComplex()->getArrayType());
        $this->assertEquals('Companies', $type->getComplex()->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $type->getComplex()->getComplex()->getElementAttributes());
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
        $this->assertEquals('ListOfAgents', $type->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'ns:ArrayOfAgents', 'name' => 'agents'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
        ), $type->getComplex()->getElementAttributes());
        $this->assertEquals('ArrayOfAgents', $type->getComplex()->getComplex()->getName());
        $this->assertEquals('ns:MocksMockUserWrapper[]', $type->getComplex()->getComplex()->getArrayType());
        $this->assertEquals('MocksMockUserWrapper', $type->getComplex()->getComplex()->getComplex()->getName());
        $this->assertEquals(array(
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id'),
            array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
            array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
        ), $type->getComplex()->getComplex()->getComplex()->getElementAttributes());
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
    	$this->assertEquals('ArrayOfNames', $element->getComplex()->getName());
    	$this->assertEquals('xsd:string[]', $element->getComplex()->getArrayType());
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
    	$this->assertEquals('Info', $element->getComplex()->getName());
    	$this->assertEquals(array(
    			array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
    			array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
    	), $element->getComplex()->getElementAttributes());
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
    	$this->assertEquals('AgentNameWithId', $element->getComplex()->getName());
    	$this->assertEquals(array(
    			array('type' => 'element', 'value' => 'ns:MocksMockUserWrapper', 'name' => 'agent'),
    			array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
    	), $element->getComplex()->getElementAttributes());
    	$this->assertEquals('AgentNameWithId', $element->getComplex()->getName());
    	$this->assertEquals(array(
    			array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id'),
    			array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
    			array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
    	), $element->getComplex()->getComplex()->getElementAttributes());
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
    	$this->assertEquals('NamesInfo', $element->getComplex()->getName());
    	$this->assertEquals(array(
    			array('type' => 'type', 'value' => 'ns:ArrayOfNames', 'name' => 'names'),
    			array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
    	), $element->getComplex()->getElementAttributes());
    	$this->assertEquals('NamesInfo', $element->getComplex()->getName());
    	$this->assertEquals('ArrayOfNames', $element->getComplex()->getComplex()->getName());
    	$this->assertEquals('xsd:string[]', $element->getComplex()->getComplex()->getArrayType());
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
    	$this->assertEquals('ArrayOfCompanies', $element->getComplex()->getName());
    	$this->assertEquals('ns:Companies[]', $element->getComplex()->getArrayType());
    	$this->assertEquals('Companies', $element->getComplex()->getComplex()->getName());
    	$this->assertEquals(array(
    			array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
    			array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
    	), $element->getComplex()->getComplex()->getElementAttributes());
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
    	$this->assertEquals('ListOfAgents', $element->getComplex()->getName());
    	$this->assertEquals(array(
    			array('type' => 'type', 'value' => 'ns:ArrayOfAgents', 'name' => 'agents'),
    			array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id')
    	), $element->getComplex()->getElementAttributes());
    	$this->assertEquals('ArrayOfAgents', $element->getComplex()->getComplex()->getName());
    	$this->assertEquals('ns:MocksMockUserWrapper[]', $element->getComplex()->getComplex()->getArrayType());
    	$this->assertEquals('MocksMockUserWrapper', $element->getComplex()->getComplex()->getComplex()->getName());
    	$this->assertEquals(array(
    			array('type' => 'type', 'value' => 'xsd:int', 'name' => 'id'),
    			array('type' => 'type', 'value' => 'xsd:string', 'name' => 'name'),
    			array('type' => 'type', 'value' => 'xsd:int', 'name' => 'age')
    	), $element->getComplex()->getComplex()->getComplex()->getElementAttributes());
    }

}
