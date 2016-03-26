<?php
/**
 * Copyright (C) 2013-2016
 * Piotr Olaszewski <piotroo89@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace WSDL;

use BadMethodCallException;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
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
        $args = Arrays::getValue($args, 0, new stdClass());
        $newArgs = array();
        $parameterNames = Arrays::map($parameters, Functions::extract()->getName());
        foreach ($parameterNames as $name) {
            if (isset($args->$name)) {
                $newArgs[] = $args->$name;
            }
        }
        return $newArgs;
    }
}
