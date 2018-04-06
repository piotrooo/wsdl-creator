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
namespace Tests\WSDL\XML\XMLStyle;

use DOMDocument;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\TestCase;
use WSDL\Parser\Node;
use WSDL\XML\XMLStyle\XMLDocumentStyle;

/**
 * XMLDocumentStyleTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class XMLDocumentStyleTest extends TestCase
{
    /**
     * @var DOMDocument
     */
    private $DOMDocument;
    /**
     * @var XMLDocumentStyle
     */
    private $XMLDocumentStyle;

    protected function setUp()
    {
        parent::setUp();
        $this->DOMDocument = new DOMDocument();
        $this->XMLDocumentStyle = new XMLDocumentStyle();
    }

    /**
     * @test
     */
    public function shouldGenerateDOMElementForBinding()
    {
        //when
        $DOMElement = $this->XMLDocumentStyle->generateBinding($this->DOMDocument, 'soap');

        //then
        $this->assertEquals('soap:binding', $DOMElement->tagName);
        $this->assertEquals('document', $DOMElement->getAttribute('style'));
        $this->assertEquals('http://schemas.xmlsoap.org/soap/http', $DOMElement->getAttribute('transport'));
    }

    /**
     * @test
     */
    public function shouldGenerateDOMElementsForMessage()
    {
        //given
        $nodes = [
            new Node('int', '$age', false),
            new Node('object', '$user', false, [new Node('string', '$name', false)]),
            new Node('string', '$numbers', true)
        ];

        //when
        $DOMElements = $this->XMLDocumentStyle->generateMessagePart($this->DOMDocument, $nodes);

        //then
        Assert::thatArray($DOMElements)->extracting('tagName')->containsExactly('part', 'part', 'part');

        $this->assertEquals('age', $DOMElements[0]->getAttribute('name'));
        $this->assertEquals('ns:age', $DOMElements[0]->getAttribute('element'));

        $this->assertEquals('user', $DOMElements[1]->getAttribute('name'));
        $this->assertEquals('ns:user', $DOMElements[1]->getAttribute('element'));

        $this->assertEquals('numbers', $DOMElements[2]->getAttribute('name'));
        $this->assertEquals('ns:numbers', $DOMElements[2]->getAttribute('element'));
    }
}
