<?php
namespace WSDL;

use BadMethodCallException;
use ReflectionMethod;
use stdClass;
use WSDL\Parser\MethodParser;
use WSDL\Types\Type;

/**
 * Provide a wrapper for
 * Provider helper class handlers serving wrapped document/literal method can
 * extend to automatically wrap the response in an appropriate wrapper
 */
class DocumentLiteralWrapper
{
    private $_obj = null;

    public function __construct($obj)
    {
        $this->_obj = $obj;
    }

    public function __call($method, $args)
    {
        if (!method_exists($this->_obj, $method)) {
            throw new BadMethodCallException('Unknown method [' . $method . ']');
        }

        $reflectedMethod = new ReflectionMethod($this->_obj, $method);
        $parsedMethod = new MethodParser($method, $reflectedMethod->getDocComment());
        $parameters = $parsedMethod->parameters();
        $returning = $parsedMethod->returning();

        $args = $this->_parseArgs($args, $parameters);
        $return = call_user_func_array(array($this->_obj, $method), $args);

        $returnVariable = $returning->getName();
        if (empty($returnVariable)) {
            return $return;
        } else {
            $obj = new stdClass();
            $obj->$returnVariable = $return;
            return $obj;
        }
    }

    private function _parseArgs($args, $parameters)
    {
        $args = isset($args[0]) ? $args[0] : new stdClass();
        $newArgs = array();
        $parameterNames = array_map(function (Type $parameter) {
            return $parameter->getName();
        }, $parameters);
        foreach ($parameterNames as $name) {
            if (isset($args->$name)) {
                $newArgs[] = $args->$name;
            }
        }
        return $newArgs;
    }
}
