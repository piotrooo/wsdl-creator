<?php
/**
 * ParserComplexType
 *
 * @author Piotr Olaszewski
 */
namespace WSDL;

class ParserComplexType 
{
    private $_parsedMethods;

    public function __construct($parsedMethods)
    {
        $this->_parsedMethods = $parsedMethods;
        print_r($parsedMethods);
    }
}