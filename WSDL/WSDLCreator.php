<?php
/**
 * WSDLCreator
 *
 * @author Piotr Olaszewski
 */
namespace WSDL;

use WSDL\Parser\ClassParser;

require_once 'Parser/ClassParser.php';
require_once 'XML/XMLWrapperGenerator.php';

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
    }

    public function parseClass()
    {
        $this->_classParser = new ClassParser($this->_class);
        $this->_classParser->parse();
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