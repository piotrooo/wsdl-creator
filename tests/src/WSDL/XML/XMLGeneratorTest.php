<?php
/**
 * XMLGeneratorTest
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
use WSDL\Parser\ClassParser;
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
    private $_targetNamespace = 'http://example.com/mocks/mockclass';
    private $_targetNamespaceTypes = 'http://example.com/mocks/mockclass/types';
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
    public function shouldGenerateDefinitions()
    {
        //given
        $matcher = array(
            'tag' => 'definitions',
            'attributes' => array(
                'name' => $this->_XMLGenerator->extractClassName($this->_class),
                'targetNamespace' => $this->_targetNamespace,
                'xmlns:tns' => $this->_targetNamespace,
                'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
                'xmlns:soap' => 'http://schemas.xmlsoap.org/wsdl/soap/',
                'xmlns' => 'http://schemas.xmlsoap.org/wsdl/',
                'xmlns:ns' => $this->_targetNamespaceTypes
            )
        );

        //then
        $this->assertTag($matcher, $this->_XML, '', false);
    }

    /**
     * @test
     */
    public function shouldGenerateMessages()
    {
        //given
        $matcher = array(
            'tag' => 'message',
            'ancestor' => array('tag' => 'definitions')
        );

        //then
        $this->assertTag($matcher, $this->_XML, '', false);
        $this->assertSelectCount('message', 6, $this->_XML);
        $this->assertSelectCount('message part', 7, $this->_XML);
    }

    /**
     * @test
     */
    public function shouldGeneratePortType()
    {
        //given
        $matcher = array(
            'tag' => 'portType',
            'ancestor' => array('tag' => 'definitions'),
            'attributes' => array('name' => 'MockClassPortType')
        );

        //then
        $this->assertTag($matcher, $this->_XML, '', false);
        $this->assertSelectCount('portType operation', 3, $this->_XML);
        $this->assertSelectCount('portType operation input', 3, $this->_XML);
        $this->assertSelectCount('portType operation output', 3, $this->_XML);
        $this->assertSelectCount('portType operation documentation', 1, $this->_XML);
    }

    /**
     * @test
     */
    public function shouldGenerateBinding()
    {
        //given
        $matcher = array(
            'tag' => 'binding',
            'ancestor' => array('tag' => 'definitions'),
            'attributes' => array(
                'name' => 'MockClassBinding',
                'type' => "tns:MockClassPortType"
            ),
            'descendant' => array(
                'tag' => 'binding',
                'attributes' => array(
                    'style' => 'rpc',
                    'transport' => "http://schemas.xmlsoap.org/soap/http"
                )
            )
        );

        //then
        $this->assertTag($matcher, $this->_XML, '', false);
        $this->assertSelectCount('binding operation', 6, $this->_XML);
    }

    public function shouldPutNamespaceOnSoapBindBody()
    {
        //given
        $matcher = array(
            'tag' => 'binding',
            'ancestor' => array('tag' => 'definitions'),
            'attributes' => array(
                'name' => 'MockClassBinding',
                'type' => "tns:MockClassPortType"
            ),
            'descendant' => array(
                'tag' => 'body',
                'attributes' => array(
                    'use' => 'literal',
                    'namespace' => $this->_targetNamespace
                )
            )
        );

        $this->assertTag($matcher, $this->_XML, '', false);
        $this->assertSelectCount('binding body@namepsace', 12, $this->_XML);
    }

    /**
     * @test
     */
    public function shouldGenerateService()
    {
        //given
        $matcher = array(
            'tag' => 'service',
            'ancestor' => array('tag' => 'definitions'),
            'attributes' => array(
                'name' => 'MockClassService'
            ),
            'descendant' => array(
                'tag' => 'port',
                'attributes' => array(
                    'name' => 'MockClassPort',
                    'binding' => 'tns:MockClassBinding'
                ),
                'child' => array(
                    'tag' => 'address',
                    'attributes' => array(
                        'location' => $this->_location
                    )
                )
            )
        );

        //then
        $this->assertTag($matcher, $this->_XML, '', false);
    }

    /**
     * @test
     */
    public function shouldRenderXML()
    {
        //when
        $this->expectOutputRegex('/definitions.*message.*portType.*binding.*service/');
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
        $matcher = array(
            'tag' => 'definitions',
            'attributes' => array(
                'name' => 'MockClassMultipleNamespace',
                'targetNamespace' => 'http://example.com/mocks/test/mockclassmultiplenamespace',
                'xmlns:tns' => 'http://example.com/mocks/test/mockclassmultiplenamespace',
                'xmlns:xsd' => 'http://www.w3.org/2001/XMLSchema',
                'xmlns:soap' => 'http://schemas.xmlsoap.org/wsdl/soap/',
                'xmlns' => 'http://schemas.xmlsoap.org/wsdl/',
                'xmlns:ns' => 'http://example.com/mocks/test/mockclassmultiplenamespace/types'
            )
        );
        $classParser = new ClassParser('\Mocks\Test\MockClassMultipleNamespace');
        $classParser->parse();
        XMLGenerator::$alreadyGeneratedComplexTypes = array();
        $xml = new XMLGenerator('\Mocks\Test\MockClassMultipleNamespace', $this->_namespace, $this->_location);
        $xml->setWSDLMethods($classParser->getMethods())->setBindingStyle(new RpcLiteral())->generate();

        //when
        $wsdl = $xml->getGeneratedXML();

        //then
        $this->assertTag($matcher, $wsdl, '', false);
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
        $this->assertRegExp('/MocksMockUserWrapper.*name="id" type="xsd:int".*name="name" type="xsd:string".*name="age" type="xsd:int"/', $wsdl);
    }
}
