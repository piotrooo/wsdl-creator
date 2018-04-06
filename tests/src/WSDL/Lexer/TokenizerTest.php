<?php
/**
 * Copyright (C) 2013-2018
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
namespace Tests\WSDL\Lexer;

use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use PHPUnit_Framework_TestCase;
use WSDL\Lexer\Token;
use WSDL\Lexer\Tokenizer;

/**
 * TokenizerTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class TokenizerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldTokenizeSimpleType()
    {
        //given
        $param = 'int $age';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                [Token::TYPE, Token::NAME, Token::EOF],
                ['int', '$age', 'eof']
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeSimpleTypeWithArray()
    {
        //given
        $param = 'string[] $name';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                [Token::TYPE, Token::ARRAYS, Token::NAME, Token::EOF],
                ['string', '[]', '$name', 'eof']
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeObject()
    {
        //given
        $param = 'object $name {string $firstName int $age}';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                [Token::TYPE, Token::NAME, Token::OPEN_OBJECT, Token::TYPE, Token::NAME, Token::TYPE, Token::NAME, Token::CLOSE_OBJECT, Token::EOF],
                ['object', '$name', '{', 'string', '$firstName', 'int', '$age', '}', 'eof']
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeObjectWithArray()
    {
        //given
        $param = 'object[] $name {string $firstName int $age}';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                [Token::TYPE, Token::ARRAYS, Token::NAME, Token::OPEN_OBJECT, Token::TYPE, Token::NAME, Token::TYPE, Token::NAME, Token::CLOSE_OBJECT, Token::EOF],
                ['object', '[]', '$name', '{', 'string', '$firstName', 'int', '$age', '}', 'eof']
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeNestedObjects()
    {
        //given
        $param = 'object $name { 
            object $user{
                string $firstName
                int $age
            }
            int $count
        }';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                [
                    Token::TYPE, Token::NAME, Token::OPEN_OBJECT,
                    Token::TYPE, Token::NAME, Token::OPEN_OBJECT,
                    Token::TYPE, Token::NAME,
                    Token::TYPE, Token::NAME,
                    Token::CLOSE_OBJECT,
                    Token::TYPE, Token::NAME,
                    Token::CLOSE_OBJECT,
                    Token::EOF
                ],
                ['object', '$name', '{', 'object', '$user', '{', 'string', '$firstName', 'int', '$age', '}', 'int', '$count', '}', 'eof']
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeClassWrapper()
    {
        //given
        $param = 'object $name { 
            className \Foo\Bar\Baz
        }';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                [
                    Token::TYPE, Token::NAME, Token::OPEN_OBJECT,
                    Token::TYPE, Token::CLASS_NAME,
                    Token::CLOSE_OBJECT,
                    Token::EOF
                ],
                ['object', '$name', '{', 'className', '\Foo\Bar\Baz', '}', 'eof']
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeClassWrapperOneLine()
    {
        //given
        $param = 'object $name { className \Foo\Bar\Baz }';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                [
                    Token::TYPE, Token::NAME, Token::OPEN_OBJECT,
                    Token::TYPE, Token::CLASS_NAME,
                    Token::CLOSE_OBJECT,
                    Token::EOF
                ],
                ['object', '$name', '{', 'className', '\Foo\Bar\Baz', '}', 'eof']
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeClassWrapperWithDuplicatedBackSlash()
    {
        //given
        $param = 'object $name { className \\Foo\\Bar\\Baz }';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                [
                    Token::TYPE, Token::NAME, Token::OPEN_OBJECT,
                    Token::TYPE, Token::CLASS_NAME,
                    Token::CLOSE_OBJECT,
                    Token::EOF
                ],
                ['object', '$name', '{', 'className', '\\Foo\\Bar\\Baz', '}', 'eof']
            );
    }

    /**
     * @test
     */
    public function shouldTokenizeClassWrapperWithoutFirstBackSlash()
    {
        //given
        $param = 'object $name { className Foo\Bar\Baz }';
        $tokenizer = new Tokenizer();

        //when
        $tokens = $tokenizer->lex($param);

        //then
        Assert::thatArray($tokens)
            ->extracting('getName()', 'getValue()')
            ->containsExactly(
                [
                    Token::TYPE, Token::NAME, Token::OPEN_OBJECT,
                    Token::TYPE, Token::CLASS_NAME,
                    Token::CLOSE_OBJECT,
                    Token::EOF
                ],
                ['object', '$name', '{', 'className', 'Foo\Bar\Baz', '}', 'eof']
            );
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenOccursUnexpectedCharacter()
    {
        //given
        $param = 'string [$message';
        $tokenizer = new Tokenizer();

        //when
        CatchException::when($tokenizer)->lex($param);

        //then
        CatchException::assertThat()->hasMessage('Unexpected character: >[< offset >7<');
    }
}
