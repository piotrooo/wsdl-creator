<?php
/**
 * WSDLCreator
 *
 * @author Piotr Olaszewski
 * @see http://www.xfront.com/GlobalVersusLocal.html
 */
namespace WSDL;

require_once 'ClassDocParser.php';
require_once 'XMLWrapperGenerator.php';

class WSDLCreator
{
    private $_class;
    private $_classParser;
    private $_soapVersion = SOAP_1_1;

    public function __construct($class)
    {
        $this->_class = $class;

        $this->parseClassDocComments();
    }

    public function parseClassDocComments()
    {
        $this->_classParser = new ClassDocParser($this->_class);
        $s = $this->_classParser->parserComplexTypes();
    }

    public function generateXML()
    {

    }

    public function renderWSDL()
    {
        header("Content-Type: text/xml");

        $methods = $this->_classParser->getAllMethods();
        $parsedComments = $this->_classParser->getParsedComments();

        $xml = new XMLWrapperGenerator('ExampleSoapServer', "http://example.com/");
        $xml
            ->setMethods($methods)
            ->setDefinitions()
            ->setMessage($parsedComments)
            ->setPortType()
            ->setBinding()
            ->setService()
        ;
        $xml->render();
    }
}