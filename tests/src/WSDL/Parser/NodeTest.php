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
namespace Tests\WSDL\Parser;

use PHPUnit\Framework\TestCase;
use WSDL\Parser\Node;

/**
 * NodeTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class NodeTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetTypeForSimpleType()
    {
        //given
        $node = new Node('int', '$age', false);

        //when
        $type = $node->getType();

        //then
        $this->assertEquals('int', $type);
    }

    /**
     * @test
     */
    public function shouldGetTypeForObjectType()
    {
        //given
        $elements = [
            new Node('string', '$name', false)
        ];
        $node = new Node('object', '$user', false, $elements);

        //when
        $type = $node->getType();

        //then
        $this->assertEquals('object', $type);
    }

    /**
     * @test
     */
    public function shouldReturnName()
    {
        //given
        $node = new Node('int', '$age', false);

        //when
        $name = $node->getName();

        //then
        $this->assertEquals('$age', $name);
    }

    /**
     * @test
     */
    public function shouldGetSanitizedName()
    {
        //given
        $node = new Node('int', '$age', false);

        //when
        $name = $node->getSanitizedName();

        //then
        $this->assertEquals('age', $name);
    }

    /**
     * @test
     */
    public function shouldGetNameForArray()
    {
        //given
        $node = new Node('int', '$age', true);

        //when
        $name = $node->getNameForArray();

        //then
        $this->assertEquals('ArrayOfAge', $name);
    }

    /**
     * @test
     */
    public function shouldGetNameForObject()
    {
        //given
        $elements = [
            new Node('string', '$name', false)
        ];
        $node = new Node('object', '$user', false, $elements);

        //when
        $name = $node->getNameForObject();

        //then
        $this->assertEquals('User', $name);
    }

    /**
     * @test
     */
    public function shouldSingularizeeNameForObject()
    {
        //given
        $elements = [
            new Node('string', '$name', false)
        ];
        $node = new Node('object', '$users', true, $elements);

        //when
        $name = $node->getNameForObject();

        //then
        $this->assertEquals('User', $name);
    }

    /**
     * @test
     */
    public function shouldReturnFalseWhenTypeIsNotArray()
    {
        //given
        $node = new Node('string', '$name', false);

        //when
        $isArray = $node->isArray();

        //then
        $this->assertFalse($isArray);
    }

    /**
     * @test
     */
    public function shouldReturnTrueWhenTypeIsArray()
    {
        //given
        $node = new Node('string', '$name', true);

        //when
        $isArray = $node->isArray();

        //then
        $this->assertTrue($isArray);
    }

    /**
     * @test
     */
    public function shouldReturnFalseWhenTypeIsNotObject()
    {
        //given
        $node = new Node('string', '$name', true);

        //when
        $isArray = $node->isObject();

        //then
        $this->assertFalse($isArray);
    }

    /**
     * @test
     */
    public function shouldReturnTrueWhenTypeIsObject()
    {
        //given
        $elements = [
            new Node('string', '$name', false)
        ];
        $node = new Node('object', '$users', true, $elements);

        //when
        $isArray = $node->isObject();

        //then
        $this->assertTrue($isArray);
    }
}
