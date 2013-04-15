<?php
/**
 * WSDLCreator
 *
 * @author Piotr Olaszewski
 */
namespace WSDL;

class WSDLCreator
{
    private $_class;
    private $_soapVersion = SOAP_1_1;

    public function __construct($class)
    {
        $this->_class = $class;
    }

    public function renderWSDL()
    {
        header("Content-Type: text/xml");
    }
}