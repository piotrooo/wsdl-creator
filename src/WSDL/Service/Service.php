<?php
/**
 * Service
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Service;

class Service
{
    private $_name;
    private $_methods;

    function __construct($name, $methods)
    {
        $this->_name = $name;
        $this->_methods = $methods;
    }

    public function render()
    {
        $this->template->methods = $this->_methods;
        require_once 'service_template.phtml';
    }
}