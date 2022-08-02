<?php
/**
 * Copyright (C) 2013-2022
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
namespace Tests\WSDL\Builder;

use PHPUnit\Framework\TestCase;
use WSDL\Builder\Parameter;
use WSDL\Lexer\Tokenizer;

class ParameterTest extends TestCase
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
