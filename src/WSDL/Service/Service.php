<?php
/**
 * Copyright (C) 2013-2016
 * Piotr Olaszewski <piotroo89@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace WSDL\Service;

use SoapClient;
use stdClass;
use WSDL\Parser\MethodParser;
use WSDL\Types\Arrays;
use WSDL\Types\Simple;

/**
 * Service
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
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
