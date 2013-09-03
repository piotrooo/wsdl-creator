<?php
/**
 * WSDLTypesObject
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
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
        foreach ($this->_method->parameters() as $parameter) {
            if ($parameter->isComplex()) {
                $complexTypes[] = $parameter->complexTypes();
            }
        }
        return $complexTypes;
    }

    public function getTypeName()
    {
        return $this->_method->getName();
    }

    public function getReturningComplexType()
    {
        return $this->_method->returning()->complexTypes();
    }
}