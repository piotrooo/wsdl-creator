<?php
namespace WSDL;

use BadMethodCallException;
use ReflectionMethod;
use stdClass;
use WSDL\Parser\MethodParser;

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

        $return = call_user_func_array(array($this->_obj, $method), $args);

        $reflectedMethod = new ReflectionMethod($this->_obj, $method);
        $parsedMethod = new MethodParser($method, $reflectedMethod->getDocComment());
        $returning = $parsedMethod->returning();
        $returnVariable = $returning->getName();

        if (empty($returnVariable)) {
            return $return;
        } else {
            $obj = new stdClass();
            $obj->{$returnVariable} = $return;
            return $obj;
        }
    }
}