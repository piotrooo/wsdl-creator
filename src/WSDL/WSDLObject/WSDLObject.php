<?php
/**
 * WSDLObject
 *
 * @author Piotr Olaszewski
 */
namespace WSDL\WSDLObject;

use WSDL\Parser\MethodParser;

class WSDLObject
{
    /**
     * @var MethodParser[]
     */
    private $_methods;

    public function setMethods($methods)
    {
        $this->_methods = $methods;
        return $this;
    }

    public function getTypes()
    {
        $types = array();
        foreach ($this->_methods as $method) {
            $types[] = new WSDLTypesObject($method);
        }
        return $types;
    }
}