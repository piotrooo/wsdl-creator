<?php
/**
 * TypeHelper
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Utilities;

use WSDL\Types\Arrays;
use WSDL\Types\Object;
use WSDL\Types\Simple;

class TypeHelper
{
    public static function isSimple($type)
    {
        return $type instanceof Simple;
    }

    public static function isArray($type)
    {
        return $type instanceof Arrays;
    }

    public static function isObject($type)
    {
        return $type instanceof Object;
    }

    public static function getXsdType($type)
    {
        return 'xsd:' . $type;
    }
}
