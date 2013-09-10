<?php
/**
 * ComplexTypeParserTest
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
use WSDL\Parser\ComplexTypeParser;

class ComplexTypeParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateComplexTypes()
    {
        //given
        $complexType = '@string=name @int=id';

        //when
        $parser = ComplexTypeParser::create($complexType);

        //then
        $this->assertCount(2, $parser);
    }

    /**
     * @test
     */
    public function shouldCorrectCreateComplexType()
    {
        //given
        $complexType = '@string=$name @int=$id';

        //when
        $parser = ComplexTypeParser::create($complexType);

        //then
        $find = current($parser);
        $this->assertEquals('string', $find->getType());
        $this->assertEquals('name', $find->getName());
    }
}