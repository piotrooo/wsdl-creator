<?php
/**
 * TypesElement
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

class TypesElement
{
    private $_name;
    private $_elementAttributes = array();
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

    public function setElementAttributes($type, $value, $name)
    {
        $this->_elementAttributes[] = array('type' => $type, 'value' => $value, 'name' => $name);
        return $this;
    }

    public function getElementAttributes()
    {
        return $this->_elementAttributes;
    }

    public function getComplex()
    {
        return $this->_complex;
    }

    public function setComplex($complex)
    {
        $this->_complex = $complex;
        return $this;
    }
}
