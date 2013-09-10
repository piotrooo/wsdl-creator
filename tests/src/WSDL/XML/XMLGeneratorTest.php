<?php
/**
 * XMLGeneratorTest
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
use WSDL\Parser\ClassParser;
use WSDL\XML\XMLGenerator;

class XMLGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var XMLGenerator
     */
    private $_XMLGenerator;
    private $_XML;
    private $_class = 'MockClass';
    private $_namespace = 'http://example.com/';
    private $_targetNamespace = 'http://example.com/mockclass';
    private $_targetNamespaceTypes = 'http://example.com/mockclass/types';
    private $_location = 'http://localhost/wsdl-creator/ExampleSoapServer.php';

    public function setUp()
    {
        parent::setUp();
        $this->markTestSkipped('Generator not fixed');
        $classParser = new ClassParser('\Mocks\MockClass');
        $classParser->parse();

        $XMLGenerator = new XMLGenerator($this->_class, $this->_namespace, $this->_location);
        $XMLGenerator->setWSDLMethods($classParser->getMethods())->generate();
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
                'name' => $this->_class,
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
        $this->assertSelectCount('message', 4, $this->_XML);
        $this->assertSelectCount('message part', 5, $this->_XML);
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
        $this->assertSelectCount('portType operation', 2, $this->_XML);
        $this->assertSelectCount('portType operation input', 2, $this->_XML);
        $this->assertSelectCount('portType operation output', 2, $this->_XML);
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
        $this->assertSelectCount('binding operation', 4, $this->_XML);
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
    public function shouldRenderXML ()
    {
        //when
        $this->expectOutputRegex('/definitions.*message.*portType.*binding.*service/');
        $this->_XMLGenerator->render();
    }
}