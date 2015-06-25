<?php
/**
 * WSDLCreator
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL;


use WSDL\Parser\ClassParser;
use WSDL\Service\Service;
use WSDL\XML\Styles\RpcLiteral;
use WSDL\XML\Styles\Style;
use WSDL\XML\XMLGenerator;

class WSDLCreator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Style
     */
    private $_bindingStyle;

    /**
     * @var ClassParser
     */
    private $_classParser;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->_bindingStyle = new RpcLiteral();
    }

    private function _parseClass()
    {
        if (empty($this->_classParser)) {
            $this->_classParser = new ClassParser($this->config);
            $this->_classParser->parse();
        }
    }

    public function renderWSDL()
    {
        $this->_parseClass();
        header("Content-Type: text/xml");
        $xml = new XMLGenerator($this->config->getClass(), $this->config->getNamespace(), $this->config->getLocation());
        $xml
            ->setWSDLMethods($this->_classParser->getMethods())
            ->setBindingStyle($this->_bindingStyle)
            ->generate();
        $xml->render();
    }

    public function renderWSDLService()
    {
        $this->_parseClass();
        $headers = apache_request_headers();
        if (empty($headers['Content-Type']) || !preg_match('#xml#i', $headers['Content-Type'])) {
            $newService = new Service($this->config->getLocation(), $this->config->getWsdlLocation(), $this->_classParser->getMethods());
            $newService->render($this->config->getClass(), $this->config->getNamespaceWithSanitizedClass());
        }
    }
}