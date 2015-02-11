<?php
/**
 * Service
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Service;

use SoapClient;
use stdClass;
use WSDL\Parser\MethodParser;
use WSDL\Types\Arrays;
use WSDL\Types\Simple;

class Service
{
    public $template;

    private $wsdl;
    /**
     * @var MethodParser[]
     */
    private $methods;
    private $location;

    public function __construct($location, $wsdl, $methods)
    {
        $this->location = $location;
        $this->wsdl = $wsdl;
        $this->methods = $methods;
    }

    public function render($name, $namespace)
    {
        $this->template = new stdClass();
        $this->template->serviceName = $name;
        $this->template->serviceNamespace = $namespace;
        $this->template->methods = $this->_wrapperMethods();
        require_once 'service_template.phtml';
    }

    private function _wrapperMethods()
    {
        $methods = array();
        foreach ($this->methods as $method) {
            $soapClient = new SoapClient($this->wsdl, array(
                'uri' => "http://foo.bar/",
                'location' => $this->location,
                'trace' => true,
                'cache_wsdl' => WSDL_CACHE_NONE
            ));
            call_user_func_array(array($soapClient, $method->getName()), $this->getParams($method));
            $methods[] = new MethodWrapper($method->getName(), $method->getRawParameters(), $method->getRawReturn(), $soapClient->__getLastRequest());
        }
        return $methods;
    }

    public function getParams(MethodParser $method)
    {
        $result = array();
        foreach ($method->parameters() as $parameter) {
            if ($parameter instanceof Simple) {
                $result[] = '?';
            }
            if ($parameter instanceof Arrays) {
                $result[] = array('?', '?', '?');
            }
        }
        return $result;
    }
}
