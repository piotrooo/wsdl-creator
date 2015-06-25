<?php
/**
 * ComplexTypeParserTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
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

    /**
     * @test
     */
    public function shouldCorrectCreateWhenPassedReflectionType()
    {
        //given
        $complexType = '@className=User';

        //when
        $parser = ComplexTypeParser::create($complexType);

        //then
        $find = current($parser);
        $this->assertEquals('className', $find->getType());
    }

    /**
     * @test
     */
    public function shouldReturnComplexName()
    {
        //when
        $parser = new ComplexTypeParser('string', 'name');

        //then
        $this->assertEquals('name', $parser->getName());
    }

    /**
     * @test
     */
    public function shouldReturnComplexType()
    {
        //when
        $parser = new ComplexTypeParser('string', 'name');

        //then
        $this->assertEquals('string', $parser->getType());
    }
}
