<?php
/**
 * ClassParserTest
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
use WSDL\Parser\ClassParser;

class ClassParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldParseOnlyPublicMethods()
    {
        //given
        $classParser = new ClassParser('\Mocks\MockClass');

        //when
        $classParser->parse();

        //then
        $this->assertCount(6, $classParser->getMethods());
    }

    /**
     * @test
     */
    public function shouldNotParseMagicMethods()
    {
        //given
        $classParser = new ClassParser('\Mocks\MockClass');

        //when
        $classParser->parse();

        //then
        $this->assertCount(6, $classParser->getMethods());
    }
}
