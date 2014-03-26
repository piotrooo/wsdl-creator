<?php
/**
 * TypesComplex
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

class TypesComplex
{
    private $_name;
    private $_arrayType;
    private $_complex;

    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setArrayType($arrayType)
    {
        $this->_arrayType = $arrayType;
        return $this;
    }

    public function getArrayType()
    {
        return $this->_arrayType;
    }

    public function setComplex($complex)
    {
        $this->_complex = $complex;
        return $this;
    }

    public function getComplex()
    {
        return $this->_complex;
    }
}