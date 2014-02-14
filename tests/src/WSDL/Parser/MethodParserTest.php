<?php
/**
 * MethodParserTest
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
use WSDL\Parser\MethodParser;

class MethodParserTest extends PHPUnit_Framework_TestCase
{
    private $_methodName;
    private $_methodDoc;

    protected function setUp()
    {
        parent::setUp();
        $this->_methodName = 'addUser';
        $this->_methodDoc = <<<'DOC'
/**
 * @desc Method to adding user
 * @param string $name
 * @param object $address @string=ip @string=mac
 * @return bool $return
 */
DOC;
    }

    /**
     * @test
     */
    public function shouldParseMethodData()
    {
        //when
        $parser = new MethodParser($this->_methodName, $this->_methodDoc);

        //then
        $this->assertEquals('Method to adding user', $parser->description());
        $this->assertCount(2, $parser->parameters());
        $this->assertInstanceOf('WSDL\Types\Simple', $parser->returning());
        $this->assertEquals($this->_methodDoc, $parser->getDoc());
        $this->assertEquals($this->_methodName, $parser->getName());
    }

    /**
     * @test
     */
    public function shouldParseMethodAndReturnRawParameters()
    {
        //when
        $parser = new MethodParser($this->_methodName, $this->_methodDoc);

        //then
        $this->assertEquals(array('string $name', 'object $address @string=ip @string=mac'), $parser->getRawParameters());
    }

    /**
     * @test
     */
    public function shouldParseMethodAndReturnRawReturn()
    {
        //when
        $parser = new MethodParser($this->_methodName, $this->_methodDoc);

        //then
        $this->assertEquals('bool $return', $parser->getRawReturn());
    }
}