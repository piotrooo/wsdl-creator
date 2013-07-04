<?php
/**
 * WSDLCreator
 *
 * @author Piotr Olaszewski
 * @see http://www.xfront.com/GlobalVersusLocal.html
 */
namespace WSDL;

use WSDL\DocParser\ClassParser;

require_once 'DocParser/ClassParser.php';
require_once 'XMLWrapperGenerator.php';

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
//        header("Content-Type: text/xml");
//
//        $methods = $this->_classParser->getAllMethods();
//
//        print_r($methods);
//
//        $parsedComments = $this->_classParser->getParsedComments();
//
//        $parserComplex = $this->_classParser->parserComplexTypes();
//        $complexTypes = $parserComplex->getComplexTypes();
//
//        $xml = new XMLWrapperGenerator('ExampleSoapServer', "http://example.com/");
//        $xml
//            ->setMethods($methods)
//            ->setParsedClass($parsedComments)
//            ->setDefinitions()
//            ->setTypes()
//            ->setMessage()
//            ->setPortType()
//            ->setBinding()
//            ->setService();
//        $xml->render();
    }
}