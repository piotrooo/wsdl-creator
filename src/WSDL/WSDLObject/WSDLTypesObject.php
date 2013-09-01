<?php
/**
 * WSDLTypesObject
 *
 * @author Piotr Olaszewski
 */
namespace WSDL\WSDLObject;

use WSDL\Parser\MethodParser;

class WSDLTypesObject
{
    /**
     * @var MethodParser
     */
    private $_method;

    public function __construct(MethodParser $method)
    {
        $this->_method = $method;
    }
}