<?php
/**
 * ParameterParserTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
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
        $create = ParameterParser::create($arrayOfParameters, '');

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
        $parser = new ParameterParser($parameter);

        //when
        $parser->parse();

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
        $parameter = 'object $object1 @string=$name @int=$id';
        $parser = new ParameterParser($parameter);

        //when
        $parser->parse();

        //then
        $this->assertEquals('object1', $parser->getName());
        $this->assertEquals('object', $parser->getType());
        $this->assertTrue($parser->isComplex());
        $this->assertCount(2, $parser->complexTypes());
    }

    /**
     * @test
     */
    public function shouldParseObjectWrapper()
    {
        //given
        $parameter = 'wrapper $user @className=\Mocks\MockUserWrapper';
        $parser = new ParameterParser($parameter);

        //when
        $parser->parse();

        //then
        $this->assertEquals('user', $parser->getName());
        $this->assertEquals('MocksMockUserWrapper', $parser->getType());
    }

    /**
     * @test
     */
    public function shouldParseObjectWrapperArray()
    {
        //given
        $parameter = 'wrapper[] $users @className=\Mocks\MockUserWrapper';
        $parser = new ParameterParser($parameter);

        //when
        $parser->parse();

        //then
        $this->assertEquals('users', $parser->getName());
        $this->assertEquals('MocksMockUserWrapper', $parser->getType());
    }

    /**
     * @test
     */
    public function shouldParseParams()
    {
        $array = array(
            'int $simple1',
            'int[] $simple2',
            'object $object1 @string=$name1 @int=$id',
            'object $object2 @(wrapper $wr1 @className=\Mocks\MockUserWrapper) @int=$id',
            'object $object3 @string[]=$name2 @int=$id',
            'object $object4 @(wrapper[] $wr2 @className=\Mocks\MockUserWrapper) @int=$id',
            'object[] $object5 @string=$name3 @int=$id',
            'object[] $object6 @string[]=$name4 @int=$id',
            'object[] $object7 @(wrapper $wr3 @className=\Mocks\MockUserWrapper) @int=$id',
            'object[] $object8 @(wrapper[] $wr4 @className=\Mocks\MockUserWrapper) @int=$id',
            'wrapper $wrapp1 @className=\Mocks\MockUserWrapper',
            'wrapper[] $wrapp2 @className=\Mocks\MockUserWrapper',
        );
        ParameterParser::create($array, 'sampleMethod');
    }
}
