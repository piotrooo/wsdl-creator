<?php
/**
 * MethodParserTest
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
use WSDL\Parser\MethodParser;

class MethodParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldParseMethodData()
    {
        //given
        $methodName = 'addUser';
        $methodDoc = <<<'DOC'
/**
 * @desc Method to adding user
 * @param string $name
 * @param object $address @string=ip @string=mac
 * @return bool $return
 */
DOC;
;
        //when
        $parser = new MethodParser($methodName, $methodDoc);

        //then
        $this->assertEquals('Method to adding user', $parser->description());
        $this->assertCount(2, $parser->parameters());
        $this->assertInstanceOf('WSDL\Parser\ParameterParser', $parser->returning());
        $this->assertEquals($methodDoc, $parser->getDoc());
        $this->assertEquals($methodName, $parser->getName());
    }
}