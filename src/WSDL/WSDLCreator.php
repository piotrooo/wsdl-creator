<?php
/**
 * WSDLCreator
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL;

use WSDL\Parser\ClassParser;
use WSDL\WSDLObject\WSDLObject;
use WSDL\XML\XMLGenerator;

class WSDLCreator
{
    private $_class;
    /**
     * @var ClassParser
     */
    private $_classParser;

    public function __construct($class)
    {
        $this->_class = $class;
        $this->_parseClass();
    }

    private function _parseClass()
    {
        $this->_classParser = new ClassParser($this->_class);
        $this->_classParser->parse();
    }

    private function _generateWSDLObject()
    {
        return new WSDLObject($this->_classParser->getMethods());
    }

    public function renderWSDL()
    {
        header("Content-Type: text/xml");
        $xml = new XMLGenerator($this->_class, "http://example.com/");
        $xml
            ->setWSDLObject($this->_generateWSDLObject())
            ->setDefinitions()
            ->setTypes()
//            ->setMessage()
//            ->setPortType()
//            ->setBinding()
            ->setService();
        $xml->render();
    }
}