<?php
/**
 * WSDLObject
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\WSDLObject;

use WSDL\Parser\MethodParser;

class WSDLObject
{
    /**
     * @var MethodParser[]
     */
    private $_methods;

    /**
     * @param MethodParser[] $methods
     */
    public function __construct($methods)
    {
        $this->_methods = $methods;
    }

    /**
     * @return WSDLTypesObject[]
     */
    public function getTypes()
    {
        $types = array();
        foreach ($this->_methods as $method) {
            if ($method->hasComplexTypes()) {
                $types[] = new WSDLTypesObject($method);
            }
        }
        return $types;
    }

    public function getMethods()
    {
        return $this->_methods;
    }
}