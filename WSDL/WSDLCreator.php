<?php
/**
 * WSDLCreator
 *
 * @author Piotr Olaszewski
 */
namespace WSDL;

require_once 'ClassDocParser.php';

class WSDLCreator
{
    private $_class;
    private $_soapVersion = SOAP_1_1;

    public function __construct($class)
    {
        $this->_class = $class;

        $this->parseClassDocComments();
    }

    public function parseClassDocComments()
    {
        $classParser = new ClassDocParser($this->_class);
    }

    public function renderWSDL()
    {
//        header("Content-Type: text/xml");
    }
}