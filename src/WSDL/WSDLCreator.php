<?php
/**
 * WSDLCreator
 *
 * @author Piotr Olaszewski
 */
namespace WSDL;

use WSDL\Parser\ClassParser;
use WSDL\WSDLObject\WSDLObject;
use WSDL\XML\XMLWrapperGenerator;

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
        $this->parseClass();
        $this->_generateWSDLObject();
    }

    public function parseClass()
    {
        $this->_classParser = new ClassParser($this->_class);
        $this->_classParser->parse();
    }

    private function _generateWSDLObject()
    {
        $object = new WSDLObject();
        $object
            ->setMethods($this->_classParser->getMethods());
//        print_r($object->getTypes());
    }

    public function renderWSDL()
    {
        header("Content-Type: text/xml");
        $methods = $this->_classParser->getMethods();
        $xml = new XMLWrapperGenerator($this->_class, "http://example.com/");
        $xml
            ->setMethods($methods)
            ->setDefinitions()
            ->setTypes()
            ->setMessage()
            ->setPortType()
            ->setBinding()
            ->setService();
        $xml->render();
    }
}