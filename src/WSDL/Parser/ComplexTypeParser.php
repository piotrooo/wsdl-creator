<?php
/**
 * ComplexTypeParser
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
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
        if (self::_isReflectionType($types)) {
            $wrapperClass = str_replace('className=', '', $types);
            $wrapperParser = new WrapperParser($wrapperClass);
            $wrapperParser->parse();
            $obj = array_merge($obj, $wrapperParser->getComplexTypes());
        } else {
            $types = trim(str_replace('@', '', $types));
            $typesArray = explode(' ', $types);
            foreach ($typesArray as $type) {
                $typeData = explode('=', $type);
                $obj[] = new self($typeData[0], $typeData[1]);
            }
        }
        return $obj;
    }

    private static function _isReflectionType($types)
    {
        return preg_match('#className#', $types);
    }
}