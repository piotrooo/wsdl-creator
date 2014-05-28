<?php
/**
 * Object
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Types;

use WSDL\Parser\ComplexTypeParser;

class Object implements Type
{
    private $_type;
    private $_name;
    /**
     * @var ComplexTypeParser[]
     */
    private $_complexType;

    public function __construct($type, $name, $complexType)
    {
        $this->_type = $type;
        $this->_name = $name;
        $this->_complexType = $complexType;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return ComplexTypeParser
     */
    public function getComplexType()
    {
        return $this->_complexType;
    }
}
