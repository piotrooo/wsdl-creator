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
namespace Tests\WSDL\XML\XMLStyle;

use DOMDocument;
use Ouzo\Tests\Assert;
use PHPUnit\Framework\TestCase;
use WSDL\Builder\Parameter;
use WSDL\Parser\Node;
use WSDL\XML\XMLStyle\XMLRpcStyle;

/**
 * XMLRpcStyleTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class XMLRpcStyleTest extends TestCase
{
    /**
     * @var DOMDocument
     */
    private $DOMDocument;
    /**
     * @var XMLRpcStyle
     */
    private $XMLRpcStyle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->DOMDocument = new DOMDocument();
        $this->XMLRpcStyle = new XMLRpcStyle();
    }

    /**
     * @test
     */
    public function shouldGenerateDOMElementForBinding()
    {
        //when
        $DOMElement = $this->XMLRpcStyle->generateBinding($this->DOMDocument, 'soap');

        //then
        $this->assertEquals('soap:binding', $DOMElement->tagName);
        $this->assertEquals('rpc', $DOMElement->getAttribute('style'));
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
        $DOMElements = $this->XMLRpcStyle->generateMessagePart($this->DOMDocument, $nodes);

        //then
        Assert::thatArray($DOMElements)->extracting('tagName')->containsExactly('part', 'part', 'part');

        $this->assertEquals('age', $DOMElements[0]->getAttribute('name'));
        $this->assertEquals('xsd:int', $DOMElements[0]->getAttribute('type'));

        $this->assertEquals('user', $DOMElements[1]->getAttribute('name'));
        $this->assertEquals('ns:User', $DOMElements[1]->getAttribute('element'));

        $this->assertEquals('numbers', $DOMElements[2]->getAttribute('name'));
        $this->assertEquals('ns:ArrayOfNumbers', $DOMElements[2]->getAttribute('type'));
    }

    /**
     * @test
     */
    public function shouldGenerateDOMElementsForTypesObject()
    {
        //given
        $parameters = [
            new Parameter(new Node('object', '$user', false, [new Node('string', '$name', false)]), false)
        ];

        //when
        $DOMElements = $this->XMLRpcStyle->generateTypes($this->DOMDocument, $parameters, 'soap');

        //then
        Assert::thatArray($DOMElements)
            ->extracting('tagName')
            ->containsExactly('xsd:element', 'xsd:complexType');

        //<xsd:element name="User" nillable="true" type="ns:User"/>
        $this->assertEquals('User', $DOMElements[0]->getAttribute('name'));
        $this->assertEquals('true', $DOMElements[0]->getAttribute('nillable'));
        $this->assertEquals('ns:User', $DOMElements[0]->getAttribute('type'));

        //<xsd:complexType name="User">
        //    <xsd:sequence>
        //        <xsd:element name="name" type="xsd:string"/>
        //    </xsd:sequence>
        //</xsd:complexType>
        $this->assertEquals('User', $DOMElements[1]->getAttribute('name'));
        $sequenceActual = $DOMElements[1]->getElementsByTagName('xsd:sequence');
        $sequenceActual = $sequenceActual->item(0);
        $this->assertEquals('xsd:sequence', $sequenceActual->tagName);
        $DOMElements1Nodes = $sequenceActual->getElementsByTagName('xsd:element');
        foreach ($DOMElements1Nodes as $DOMElements1Node) {
            $this->assertEquals('xsd:element', $DOMElements1Node->tagName);
            $this->assertEquals('name', $DOMElements1Node->getAttribute('name'));
            $this->assertEquals('xsd:string', $DOMElements1Node->getAttribute('type'));
        }
    }

    /**
     * @test
     */
    public function shouldGenerateDOMElementsForTypesArrayOfSimpleType()
    {
        //given
        $parameters = [
            new Parameter(new Node('string', '$numbers', true), true)
        ];

        //when
        $DOMElements = $this->XMLRpcStyle->generateTypes($this->DOMDocument, $parameters, 'soap');

        //then
        Assert::thatArray($DOMElements)
            ->extracting('tagName')
            ->containsExactly('xsd:complexType');

        //<xsd:complexType name="ArrayOfNumbers">
        //    <xsd:complexContent>
        //        <xsd:restriction base="soapenc:Array">
        //            <xsd:attribute ref="soapenc:arrayType" soap:arrayType="xsd:string[]"/>
        //        </xsd:restriction>
        //    </xsd:complexContent>
        //</xsd:complexType>
        $this->assertEquals('ArrayOfNumbers', $DOMElements[0]->getAttribute('name'));
        $complexContentActual = $DOMElements[0]->getElementsByTagName('xsd:complexContent');
        $complexContentActual = $complexContentActual->item(0);
        $this->assertEquals('xsd:complexContent', $complexContentActual->tagName);
        $restrictionActual = $complexContentActual->getElementsByTagName('xsd:restriction');
        $restrictionActual = $restrictionActual->item(0);
        $this->assertEquals('xsd:restriction', $restrictionActual->tagName);
        $this->assertEquals('soapenc:Array', $restrictionActual->getAttribute('base'));
        $attributeActual = $restrictionActual->getElementsByTagName('xsd:attribute');
        $attributeActual = $attributeActual->item(0);
        $this->assertEquals('xsd:attribute', $attributeActual->tagName);
        $this->assertEquals('soapenc:arrayType', $attributeActual->getAttribute('ref'));
        $this->assertEquals('xsd:string[]', $attributeActual->getAttribute('soap:arrayType'));
    }

    /**
     * @test
     */
    public function shouldGenerateDOMElementsForTypesArrayOfObjectType()
    {
        //given
        $parameters = [
            new Parameter(new Node('object', '$users', true, [new Node('string', '$name', false)]), true)
        ];

        //when
        $DOMElements = $this->XMLRpcStyle->generateTypes($this->DOMDocument, $parameters, 'soap');

        //then
        Assert::thatArray($DOMElements)
            ->extracting('tagName')
            ->containsExactly('xsd:complexType', 'xsd:element', 'xsd:complexType');

        //<xsd:complexType name="ArrayOfUsers">
        //    <xsd:complexContent>
        //        <xsd:restriction base="soapenc:Array">
        //            <xsd:attribute ref="soapenc:arrayType" soap:arrayType="ns:User[]"/>
        //        </xsd:restriction>
        //    </xsd:complexContent>
        //</xsd:complexType>
        $this->assertEquals('ArrayOfUsers', $DOMElements[0]->getAttribute('name'));
        $complexContentActual = $DOMElements[0]->getElementsByTagName('xsd:complexContent');
        $complexContentActual = $complexContentActual->item(0);
        $this->assertEquals('xsd:complexContent', $complexContentActual->tagName);
        $restrictionActual = $complexContentActual->getElementsByTagName('xsd:restriction');
        $restrictionActual = $restrictionActual->item(0);
        $this->assertEquals('xsd:restriction', $restrictionActual->tagName);
        $this->assertEquals('soapenc:Array', $restrictionActual->getAttribute('base'));
        $attributeActual = $restrictionActual->getElementsByTagName('xsd:attribute');
        $attributeActual = $attributeActual->item(0);
        $this->assertEquals('xsd:attribute', $attributeActual->tagName);
        $this->assertEquals('soapenc:arrayType', $attributeActual->getAttribute('ref'));
        $this->assertEquals('ns:User[]', $attributeActual->getAttribute('soap:arrayType'));

        //<xsd:element name="User" nillable="true" type="ns:User"/>
        $this->assertEquals('User', $DOMElements[1]->getAttribute('name'));
        $this->assertEquals('true', $DOMElements[1]->getAttribute('nillable'));
        $this->assertEquals('ns:User', $DOMElements[1]->getAttribute('type'));

        //<xsd:complexType name="User">
        //    <xsd:sequence>
        //        <xsd:element name="name" type="xsd:string"/>
        //    </xsd:sequence>
        //</xsd:complexType>
        $this->assertEquals('User', $DOMElements[2]->getAttribute('name'));
        $sequenceActual = $DOMElements[2]->getElementsByTagName('xsd:sequence');
        $sequenceActual = $sequenceActual->item(0);
        $this->assertEquals('xsd:sequence', $sequenceActual->tagName);

        $DOMElements1Nodes = $sequenceActual->getElementsByTagName('xsd:element');
        foreach ($DOMElements1Nodes as $DOMElements1Node) {
            $this->assertEquals('xsd:element', $DOMElements1Node->tagName);
            $this->assertEquals('name', $DOMElements1Node->getAttribute('name'));
            $this->assertEquals('xsd:string', $DOMElements1Node->getAttribute('type'));
        }
    }

    /**
     * @test
     */
    public function shouldGenerateDOMElementsForTypesObjectWithSimpleTypeAndObjectInside()
    {
        //given
        $elements = [
            new Node('string', '$name', false),
            new Node('object', '$agent', false, [new Node('int', '$number', false)])
        ];
        $parameters = [
            new Parameter(new Node('object', '$user', false, $elements), true)
        ];

        //when
        $DOMElements = $this->XMLRpcStyle->generateTypes($this->DOMDocument, $parameters, 'soap');

        //then
        Assert::thatArray($DOMElements)
            ->extracting('tagName')
            ->containsExactly('xsd:element', 'xsd:complexType', 'xsd:element', 'xsd:complexType');

        //<xsd:element name="User" nillable="true" type="ns:User"/>
        $this->assertEquals('User', $DOMElements[0]->getAttribute('name'));
        $this->assertEquals('true', $DOMElements[0]->getAttribute('nillable'));
        $this->assertEquals('ns:User', $DOMElements[0]->getAttribute('type'));

        //<xsd:complexType name="User">
        //    <xsd:sequence>
        //        <xsd:element name="name" type="xsd:string"/>
        //        <xsd:element name="name" element="ns:Agent"/>
        //    </xsd:sequence>
        //</xsd:complexType>
        $this->assertEquals('User', $DOMElements[1]->getAttribute('name'));
        $sequenceActual = $DOMElements[1]->getElementsByTagName('xsd:sequence');
        $sequenceActual = $sequenceActual->item(0);
        $this->assertEquals('xsd:sequence', $sequenceActual->tagName);

        $DOMElementsNodes = $sequenceActual->getElementsByTagName('xsd:element');
        $DOMElements1Nodes = $DOMElementsNodes->item(0);
        $this->assertEquals('xsd:element', $DOMElements1Nodes->tagName);
        $this->assertEquals('name', $DOMElements1Nodes->getAttribute('name'));
        $this->assertEquals('xsd:string', $DOMElements1Nodes->getAttribute('type'));
        $DOMElements2Nodes = $DOMElementsNodes->item(1);
        $this->assertEquals('xsd:element', $DOMElements2Nodes->tagName);
        $this->assertEquals('agent', $DOMElements2Nodes->getAttribute('name'));
        $this->assertEquals('ns:Agent', $DOMElements2Nodes->getAttribute('element'));

        //<xsd:element name="Agent" nillable="true" type="ns:Agent"/>
        $this->assertEquals('Agent', $DOMElements[2]->getAttribute('name'));
        $this->assertEquals('true', $DOMElements[2]->getAttribute('nillable'));
        $this->assertEquals('ns:Agent', $DOMElements[2]->getAttribute('type'));

        //<xsd:complexType name="Agent">
        //    <xsd:sequence>
        //        <xsd:element name="number" type="xsd:int"/>
        //    </xsd:sequence>
        //</xsd:complexType>
        $this->assertEquals('Agent', $DOMElements[3]->getAttribute('name'));
        $sequenceActual = $DOMElements[3]->getElementsByTagName('xsd:sequence');
        $sequenceActual = $sequenceActual->item(0);
        $this->assertEquals('xsd:sequence', $sequenceActual->tagName);

        $DOMElementsNodes = $sequenceActual->getElementsByTagName('xsd:element');
        $DOMElements1Nodes = $DOMElementsNodes->item(0);
        $this->assertEquals('xsd:element', $DOMElements1Nodes->tagName);
        $this->assertEquals('number', $DOMElements1Nodes->getAttribute('name'));
        $this->assertEquals('xsd:int', $DOMElements1Nodes->getAttribute('type'));
    }

    /**
     * @test
     */
    public function shouldGenerateDOMElementsForTypesObjectWithArrayOfSimpleInside()
    {
        //given
        $elements = [
            new Node('string', '$names', true)
        ];
        $parameters = [
            new Parameter(new Node('object', '$user', false, $elements), true)
        ];

        //when
        $DOMElements = $this->XMLRpcStyle->generateTypes($this->DOMDocument, $parameters, 'soap');

        //then
        Assert::thatArray($DOMElements)
            ->extracting('tagName')
            ->containsExactly('xsd:element', 'xsd:complexType', 'xsd:complexType');

        //<xsd:element name="User" nillable="true" type="ns:User"/>
        $this->assertEquals('User', $DOMElements[0]->getAttribute('name'));
        $this->assertEquals('true', $DOMElements[0]->getAttribute('nillable'));
        $this->assertEquals('ns:User', $DOMElements[0]->getAttribute('type'));

        //<xsd:complexType name="User">
        //    <xsd:sequence>
        //        <xsd:element name="names" type="ns:ArrayOdNames"/>
        //    </xsd:sequence>
        //</xsd:complexType>
        $this->assertEquals('User', $DOMElements[1]->getAttribute('name'));
        $sequenceActual = $DOMElements[1]->getElementsByTagName('xsd:sequence');
        $sequenceActual = $sequenceActual->item(0);
        $this->assertEquals('xsd:sequence', $sequenceActual->tagName);

        $DOMElementsNodes = $sequenceActual->getElementsByTagName('xsd:element');
        $DOMElements1Nodes = $DOMElementsNodes->item(0);
        $this->assertEquals('xsd:element', $DOMElements1Nodes->tagName);
        $this->assertEquals('names', $DOMElements1Nodes->getAttribute('name'));
        $this->assertEquals('ns:ArrayOfNames', $DOMElements1Nodes->getAttribute('type'));

        //<xsd:complexType name="ArrayOfNames">
        //    <xsd:complexContent>
        //        <xsd:restriction base="soapenc:Array">
        //            <xsd:attribute ref="soapenc:arrayType" soap:arrayType="ns:User[]"/>
        //        </xsd:restriction>
        //    </xsd:complexContent>
        //</xsd:complexType>
        $this->assertEquals('ArrayOfNames', $DOMElements[2]->getAttribute('name'));
        $complexContentActual = $DOMElements[2]->getElementsByTagName('xsd:complexContent');
        $complexContentActual = $complexContentActual->item(0);
        $this->assertEquals('xsd:complexContent', $complexContentActual->tagName);
        $restrictionActual = $complexContentActual->getElementsByTagName('xsd:restriction');
        $restrictionActual = $restrictionActual->item(0);
        $this->assertEquals('xsd:restriction', $restrictionActual->tagName);
        $this->assertEquals('soapenc:Array', $restrictionActual->getAttribute('base'));
        $attributeActual = $restrictionActual->getElementsByTagName('xsd:attribute');
        $attributeActual = $attributeActual->item(0);
        $this->assertEquals('xsd:attribute', $attributeActual->tagName);
        $this->assertEquals('soapenc:arrayType', $attributeActual->getAttribute('ref'));
        $this->assertEquals('xsd:string[]', $attributeActual->getAttribute('soap:arrayType'));
    }
}
