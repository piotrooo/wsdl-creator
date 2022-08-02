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
namespace Tests\WSDL\Lexer;

use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Utilities\Arrays;
use PHPUnit\Framework\TestCase;
use WSDL\Lexer\Tokenizer;
use WSDL\Parser\Node;
use WSDL\Parser\Parser;

/**
 * ParserTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class ParserTest extends TestCase
{
    /**
     * @test
     */
    public function shouldParseSimple()
    {
        //given
        $parser = $this->parser('int $age');

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(['int', '$age', false, []]);
    }

    /**
     * @test
     */
    public function shouldParseSimpleArray()
    {
        //given
        $parser = $this->parser('int[] $age');

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(['int', '$age', true, []]);
    }

    /**
     * @test
     */
    public function shouldParseObject()
    {
        //given
        $parser = $this->parser('object $user { string $name bool $isActive }');

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray')
            ->containsExactly(['object', '$user', false]);

        /** @var Node $node */
        $node = Arrays::first($nodes);
        Assert::thatArray($node->getElements())
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(
                ['string', '$name', false, []],
                ['bool', '$isActive', false, []]
            );
    }

    /**
     * @test
     */
    public function shouldParseObjectArray()
    {
        //given
        $parser = $this->parser('object[] $user { string $name bool $isActive }');

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray')
            ->containsExactly(['object', '$user', true]);

        /** @var Node $node */
        $node = Arrays::first($nodes);
        Assert::thatArray($node->getElements())
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(
                ['string', '$name', false, []],
                ['bool', '$isActive', false, []]
            );
    }

    /**
     * @test
     */
    public function shouldParseSimpleAndObject()
    {
        //given
        $parser = $this->parser('int $age 
        object $user { string $name bool $isActive }');

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray')
            ->containsExactly(
                ['int', '$age', false],
                ['object', '$user', false]
            );

        /** @var Node $node */
        $node = $nodes[0];
        $this->assertEmpty($node->getElements());

        /** @var Node $node */
        $node = $nodes[1];
        Assert::thatArray($node->getElements())
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(
                ['string', '$name', false, []],
                ['bool', '$isActive', false, []]
            );
    }

    /**
     * @test
     */
    public function shouldParseObjectWithArrayInsideAttribute()
    {
        //given
        $parser = $this->parser('object $user { string[] $name bool $isActive }');

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray')
            ->containsExactly(['object', '$user', false]);

        /** @var Node $node */
        $node = Arrays::first($nodes);
        Assert::thatArray($node->getElements())
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(
                ['string', '$name', true, []],
                ['bool', '$isActive', false, []]
            );
    }

    /**
     * @test
     */
    public function shouldParseObjectWithClassName()
    {
        //given
        $parser = $this->parser('object $user { className \Foo\Bar\Baz }');

        //when
        $nodes = $parser->S();

        //then
        Assert::thatArray($nodes)
            ->extracting('type', 'name', 'isArray')
            ->containsExactly(['object', '$user', false]);

        /** @var Node $node */
        $node = Arrays::first($nodes);
        Assert::thatArray($node->getElements())
            ->extracting('type', 'name', 'isArray', 'elements')
            ->containsExactly(['className', '\Foo\Bar\Baz', false, []]);
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenSimpleTypeNotHaveType()
    {
        //given
        $parser = $this->parser('$name');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()
            ->isInstanceOf('\WSDL\Parser\ParserException')
            ->hasMessage('Wrong type');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenSimpleTypeNotHaveName()
    {
        //given
        $parser = $this->parser('int');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()
            ->isInstanceOf('\WSDL\Parser\ParserException')
            ->hasMessage('Wrong name');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenObjectNotHaveType()
    {
        //given
        $parser = $this->parser('$user { int $age }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()
            ->isInstanceOf('\WSDL\Parser\ParserException')
            ->hasMessage('Wrong type');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenObjectNotHaveName()
    {
        //given
        $parser = $this->parser('object { int $age }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()
            ->isInstanceOf('\WSDL\Parser\ParserException')
            ->hasMessage('Wrong name');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenInsideObjectAttributeNotHaveType()
    {
        //given
        $parser = $this->parser('object $user { $age }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()
            ->isInstanceOf('\WSDL\Parser\ParserException')
            ->hasMessage('Wrong type');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenInsideObjectAttributeNotHaveName()
    {
        //given
        $parser = $this->parser('object $user { int }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()
            ->isInstanceOf('\WSDL\Parser\ParserException')
            ->hasMessage('Wrong name');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenObjectNotHaveOpen()
    {
        //given
        $parser = $this->parser('object $user int $age }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()
            ->isInstanceOf('\WSDL\Parser\ParserException')
            ->hasMessage('Missing open object');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenObjectNotHaveOpenInNestedObject()
    {
        //given
        $parser = $this->parser('object $user { 
            int $age 
            object $role 
                int $id
                string $name
            }
        }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()
            ->isInstanceOf('\WSDL\Parser\ParserException')
            ->hasMessage('Missing open object');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenObjectNotHaveClose()
    {
        //given
        $parser = $this->parser('object $user { int $age ');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()
            ->isInstanceOf('\WSDL\Parser\ParserException')
            ->hasMessage('Missing close object');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenObjectNotHaveCloseInNestedObject()
    {
        //given
        $parser = $this->parser('object $user { 
            int $age 
            object $role {
                int $id
                string $name
        }');

        //when
        CatchException::when($parser)->S();

        //then
        CatchException::assertThat()
            ->isInstanceOf('\WSDL\Parser\ParserException')
            ->hasMessage('Missing close object');
    }

    private function parser($string)
    {
        $tokenizer = new Tokenizer();
        $tokens = $tokenizer->lex($string);

        return new Parser($tokens);
    }
}
