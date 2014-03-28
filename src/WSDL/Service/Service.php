<?php
/**
 * Service
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Service;

/**
 * @property mixed template
 */
class Service
{
    private $_name;
    private $_methods;
    private $_namespace;

    function __construct($name, $methods, $namespace)
    {
        $this->_name = $name;
        $this->_methods = $methods;
        $this->_namespace = $namespace;
    }

    public function render()
    {
        $this->template = (object)null;
        $this->template->serviceName = $this->_name;
        $this->template->serviceUrl = $this->_namespace . strtolower($this->_name);
        $this->template->methods = $this->_methods;
        require_once 'service_template.phtml';
    }
}