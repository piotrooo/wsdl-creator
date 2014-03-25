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

    public function setName($name)
    {
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }


}