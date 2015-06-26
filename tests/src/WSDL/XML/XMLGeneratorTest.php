<?php
/**
 * Copyright (C) 2013-2015
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
use Ouzo\Utilities\Path;
use WSDL\Parser\ClassParser;
use WSDL\XML\Styles\DocumentLiteralWrapped;
use WSDL\XML\Styles\RpcEncoded;
use WSDL\XML\Styles\RpcLiteral;
use WSDL\XML\XMLGenerator;

/**
 * XMLGeneratorTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class XMLGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var XMLGenerator
     */
    private $_XMLGenerator;
    private $_XML;
    private $_class = '\Mocks\MockClass';
    private $_namespace = 'http://example.com/';
    private $_location = 'http://localhost/wsdl-creator/ExampleSoapServer.php';

    public function setUp()
    {
        parent::setUp();
        $classParser = new ClassParser('\Mocks\MockClass');
        $classParser->parse();

        XMLGenerator::$alreadyGeneratedComplexTypes = array();
        $XMLGenerator = new XMLGenerator($this->_class, $this->_namespace, $this->_location);
        $XMLGenerator->setWSDLMethods($classParser->getMethods())->setBindingStyle(new RpcLiteral())->generate();
        $this->_XMLGenerator = $XMLGenerator;
        $this->_XML = $XMLGenerator->getGeneratedXML();
    }

    /**
     * @test
     */
    public function shouldGenerateWsdl()
    {
        //then
        $file = Path::join(__DIR__, 'xml_file_asserts', 'correct_wsdl.wsdl');
        $this->assertXmlStringEqualsXmlFile($file, $this->_XML);
    }

    /**
     * @test
     */
    public function shouldRenderXML()
    {
        //when
        $this->expectOutputRegex('/definitions.*message.*portType.*binding.*service/is');
        $this->_XMLGenerator->render();
    }

    /**
     * @test
     */
    public function shouldSanitizeClass()
    {
        //given
        $xml = new XMLGenerator('\Mocks\Test\MockClassMultipleNamespace', '', '');

        //when
        $sanitized = $xml->sanitizeClassName('http://foo.bar/', '\Mocks\Test\MockClassMultipleNamespace');

        //then
        $this->assertEquals('http://foo.bar/mocks/test/mockclassmultiplenamespace', $sanitized);
    }

    /**
     * @test
     */
    public function shouldCreateWSDLWithCorrectNamespace()
    {
        //given
        $classParser = new ClassParser('\Mocks\Test\MockClassMultipleNamespace');
        $classParser->parse();
        XMLGenerator::$alreadyGeneratedComplexTypes = array();
        $xml = new XMLGenerator('\Mocks\Test\MockClassMultipleNamespace', $this->_namespace, $this->_location);
        $xml->setWSDLMethods($classParser->getMethods())->setBindingStyle(new RpcLiteral())->generate();

        //when
        $wsdl = $xml->getGeneratedXML();

        //then
        $file = Path::join(__DIR__, 'xml_file_asserts', 'correct_multi_class_wsdl.wsdl');
        $this->assertXmlStringEqualsXmlFile($file, $wsdl);
    }

    /**
     * @test
     */
    public function shouldCorrectParseWrapperElement()
    {
        //given
        XMLGenerator::$alreadyGeneratedComplexTypes = array();
        $classParser = new ClassParser('\Mocks\MockClass');
        $classParser->parse();
        $xml = new XMLGenerator('\Mocks\MockClass', $this->_namespace, $this->_location);
        $xml->setWSDLMethods($classParser->getMethods())->setBindingStyle(new RpcLiteral())->generate();

        //when
        $wsdl = $xml->getGeneratedXML();

        //then
        $this->assertRegExp('/MocksMockUserWrapper.*name="id" type="xsd:int".*name="name" type="xsd:string".*name="age" type="xsd:int"/is', $wsdl);
    }

    /**
     * @test
     */
    public function shouldAddEncodingUriForRpcEncoded()
    {
        //given
        XMLGenerator::$alreadyGeneratedComplexTypes = array();
        $classParser = new ClassParser('\Mocks\MockClass');
        $classParser->parse();
        $xml = new XMLGenerator('\Mocks\MockClass', $this->_namespace, $this->_location);
        $xml->setWSDLMethods($classParser->getMethods())->setBindingStyle(new RpcEncoded())->generate();

        //when
        $wsdl = $xml->getGeneratedXML();

        //then
        $file = Path::join(__DIR__, 'xml_file_asserts', 'correct_rpc_encoded_wsdl.wsdl');
        $this->assertXmlStringEqualsXmlFile($file, $wsdl);
    }

    /**
     * @test
     */
    public function shouldCorrectCreateWsdlWithMultipleWrappersForDocumentLiteralWrapped()
    {
        //given
        XMLGenerator::$alreadyGeneratedComplexTypes = array();
        $classParser = new ClassParser('\Mocks\MockMultipleWrappers');
        $classParser->parse();
        $xml = new XMLGenerator('\Mocks\MockMultipleWrappers', $this->_namespace, $this->_location);
        $xml->setWSDLMethods($classParser->getMethods())->setBindingStyle(new DocumentLiteralWrapped())->generate();

        //when
        $wsdl = $xml->getGeneratedXML();

        //then
        $file = Path::join(__DIR__, 'xml_file_asserts', 'multiple_wrappers.wsdl');
        $this->assertXmlStringEqualsXmlFile($file, $wsdl);
    }
}
