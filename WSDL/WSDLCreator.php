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
    }

    public function generateXML()
    {

    }

    public function renderWSDL()
    {
        header("Content-Type: text/xml");

        $methods = $this->_classParser->getAllMethods();

        $xml = new XMLWrapperGenerator('ExampleSoapServer', "http://example.com/stockquote.wsdl");
        $xml
            ->setDefinitions()
            ->setBinding($methods)
        ;
        $xml->render();
    }
}