<?php
/**
 * ParameterParserTest
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
use WSDL\Parser\ParameterParser;

class ParameterParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateParametersFromArray()
    {
        //given
        $arrayOfParameters = array(
            'int $a',
            'string $b',
            'object $object1 @string=name @int=id'
        );

        //when
        $create = ParameterParser::create($arrayOfParameters);

        //then
        $this->assertCount(3, $create);
    }

    /**
     * @test
     */
    public function shouldParseSimpleType()
    {
        //given
        $parameter = 'int $a';

        //when
        $parser = new ParameterParser($parameter);

        //then
        $this->assertEquals('a', $parser->getName());
        $this->assertEquals('int', $parser->getType());
        $this->assertFalse($parser->isComplex());
    }

    /**
     * @test
     * @expectedException \WSDL\Parser\ParameterParserException
     */
    public function shouldThrowExceptionIfNotComplexType()
    {
        //given
        $parameter = 'int $a';
        $parser = new ParameterParser($parameter);

        //when
        $parser->complexTypes();
    }

    /**
     * @test
     */
    public function shouldParseComplexType()
    {
        //given
        $parameter = 'object $object1 @string=name @int=id';

        //when
        $parser = new ParameterParser($parameter);

        //then
        $this->assertEquals('object1', $parser->getName());
        $this->assertEquals('object', $parser->getType());
        $this->assertTrue($parser->isComplex());
        $this->assertCount(2, $parser->complexTypes());
    }
}