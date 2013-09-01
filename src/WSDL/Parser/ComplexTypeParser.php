<?php
/**
 * ComplexTypeParser
 *
 * @author Piotr Olaszewski
 */
namespace WSDL\Parser;

class ComplexTypeParser
{
    private $_type;
    private $_name;

    public function __construct($type, $name)
    {
        $this->_type = $type;
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param string $types
     * @return ComplexTypeParser[]
     */
    public static function create($types)
    {
        $obj = array();
        $types = trim(str_replace('@', '', $types));
        $typesArray = explode(' ', $types);
        foreach ($typesArray as $type) {
            $typeData = explode('=', $type);
            $obj[] = new self($typeData[0], $typeData[1]);
        }
        return $obj;
    }
}