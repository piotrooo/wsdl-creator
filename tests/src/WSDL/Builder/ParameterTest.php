<?php
namespace Tests\WSDL\Builder;

use PHPUnit_Framework_TestCase;
use WSDL\Builder\Parameter;
use WSDL\Lexer\Tokenizer;

class ParameterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateParameterFromTokens()
    {
        //given
        $tokenizer = new Tokenizer();

        //when
        $parameter = Parameter::fromTokens($tokenizer->lex('int[] $numbers'));

        //then
        $this->assertEquals('int', $parameter->getNode()->getType());
        $this->assertEquals('$numbers', $parameter->getNode()->getName());
        $this->assertFalse($parameter->isHeader());
    }

    /**
     * @test
     */
    public function shouldCreateParameterFromTokensWhenParameterIsHeader()
    {
        //given
        $tokenizer = new Tokenizer();

        //when
        $parameter = Parameter::fromTokens($tokenizer->lex('int[] $numbers'), true);

        //then
        $this->assertTrue($parameter->isHeader());
    }
}
