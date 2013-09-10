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
        preg_match_all('#@(\((?:.+)\)|(?:.+?))(?: |$)#', $types, $matches);
        $typesArray = $matches[1];
        $obj = array_map(function ($type) {
            if (self::_isReflectionType($type)) {
                $type = str_replace(array('(', ')'), '', $type);
            } else {
                $type = str_replace('=', ' ', $type);
            }
            $parser = new ParameterParser($type, '');
            return $parser->parse();
        }, $typesArray);
        return $obj;
    }

    private static function _isReflectionType($types)
    {
        return preg_match('#className#', $types);
    }
}