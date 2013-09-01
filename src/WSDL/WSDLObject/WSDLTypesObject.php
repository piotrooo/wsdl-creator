<?php
/**
 * WSDLTypesObject
 *
 * @author Piotr Olaszewski
 */
namespace WSDL\WSDLObject;

use WSDL\Parser\ComplexTypeParser;
use WSDL\Parser\MethodParser;
use WSDL\Parser\ParameterParser;

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

    /**
     * @return ComplexTypeParser[]
     */
    public function getComplexTypes()
    {
        $complexTypes = array();
        foreach ($this->_method->parameters() as $paramater) {
            if ($paramater->isComplex()) {
                $complexTypes = $paramater->complexTypes();
            }
        }
        return $complexTypes;
    }

    public function getTypeName()
    {
        return $this->_method->getName();
    }
}