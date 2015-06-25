<?php
/**
 * XMLGeneratorTest
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
use WSDL\Parser\ClassParser;
use WSDL\XML\Styles\RpcEncoded;
use WSDL\XML\Styles\RpcLiteral;
use WSDL\XML\XMLGenerator;

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
        $file = __DIR__ . '/correct_wsdl.wsdl';
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
        $file = __DIR__ . '/correct_multi_class_wsdl.wsdl';
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
        $file = __DIR__ . '/correct_rpc_encoded_wsdl.wsdl';
        $this->assertXmlStringEqualsXmlFile($file, $wsdl);
    }
}
