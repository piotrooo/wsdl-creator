<?php
/**
 * WSDLCreator
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL;

use Exception;
use WSDL\Parser\ClassParser;
use WSDL\Service\Service;
use WSDL\Utilities\Strings;
use WSDL\XML\Styles\RpcLiteral;
use WSDL\XML\Styles\Style;
use WSDL\XML\XMLGenerator;

class WSDLCreator
{
    private $_class;
    private $_location;
    /**
     * @var ClassParser
     */
    private $_classParser;
    private $_namespace = 'http://example.com/';
    /**
     * @var Style
     */
    private $_bindingStyle;
    /**
     * @var string
     */
    private $_locationSuffix;

    public function __construct($class, $location, $locationSuffix = 'wsdl')
    {
        $this->_class = $class;
        $this->_location = $location;
        $this->_locationSuffix = $locationSuffix;
        $this->_bindingStyle = new RpcLiteral();
        $this->_parseClass();
    }

    private function _parseClass()
    {
        $this->_classParser = new ClassParser($this->_class);
        $this->_classParser->parse();
    }

    public function renderWSDL()
    {
        header("Content-Type: text/xml");
        $xml = new XMLGenerator($this->_class, $this->_namespace, $this->_location);
        $xml
            ->setWSDLMethods($this->_classParser->getMethods())
            ->setBindingStyle($this->_bindingStyle)
            ->generate();
        $xml->render();
    }

    public function setNamespace($namespace)
    {
        $namespace = $this->_addSlashAtEndIfNotExists($namespace);
        $this->_namespace = $namespace;
        return $this;
    }

    public function getNamespaceWithSanitizedClass()
    {
        return Strings::sanitizedNamespaceWithClass($this->_namespace, $this->_class);
    }

    private function _addSlashAtEndIfNotExists($namespace)
    {
        return rtrim($namespace, '/') . '/';
    }

    public function setBindingStyle(Style $style = null)
    {
        if (!$style) {
            throw new Exception('Incorrect binding style.');
        }
        $this->_bindingStyle = $style;
        return $this;
    }

    public function renderWSDLService()
    {
        $headers = getallheaders();
        if (empty($headers['Content-Type']) || !preg_match('#xml#i', $headers['Content-Type'])) {
            $newService = new Service($this->_location, $this->getWsdlLocation(), $this->_classParser->getMethods());
            $newService->render($this->_class, $this->getNamespaceWithSanitizedClass());
        }
    }

    public function getLocation()
    {
        return $this->_location;
    }

    public function getWsdlLocation()
    {
        return $this->_location . '?' . $this->_locationSuffix;
    }
}
