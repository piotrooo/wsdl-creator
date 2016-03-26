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
namespace WSDL\Parser;

use Ouzo\Utilities\Arrays as OuzoArrays;
use WSDL\Types\Arrays;
use WSDL\Types\Object;
use WSDL\Types\Simple;

/**
 * ParameterParser
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class ParameterParser
{
    private $_strategy;
    private $_parameter;
    private $_type;
    private $_name;
    private $_methodName;

    public function __construct($parameter, $methodName = '')
    {
        $this->_parameter = trim($parameter);
        $this->_methodName = $methodName;
    }

    public function parse()
    {
        return $this->_detectType();
    }

    private function _detectType()
    {
        $this->_parseAndSetType();
        $this->_parseAndSetName();

        switch ($this->_strategy) {
            case 'object':
                return new Object($this->getType(), $this->getName(), $this->complexTypes());
            case 'wrapper':
                return $this->_createWrapperObject();
            case 'array':
                return $this->_createArrayObject();
            default:
                return new Simple($this->getType(), $this->getName());
        }
    }

    private function _parseAndSetType()
    {
        if (preg_match('#^(\w*)\[\]#', $this->_parameter, $type)) {
            $this->_type = $type[1];
            $this->_strategy = 'array';
        } else {
            preg_match('#(\w+)#', $this->_parameter, $type);
            if (isset($type[1])) {
                $this->_type = $type[1];
                $this->_strategy = $type[1];
            } else {
                $this->_type = 'void';
                $this->_strategy = 'void';
            }
        }
    }

    private function _parseAndSetName()
    {
        preg_match('#\\$(\w+)#', $this->_parameter, $name);
        $this->_name = OuzoArrays::getValue($name, 1, '');
    }

    private function _createWrapperObject()
    {
        $wrapper = $this->wrapper();
        $object = null;
        if ($wrapper->getComplexTypes()) {
            $object = new Object($this->getType(), $this->getName(), $wrapper->getComplexTypes());
        }
        return new Object($this->getType(), $this->getName(), $object);
    }

    private function _createArrayObject()
    {
        $object = null;
        if ($this->_type == 'wrapper') {
            $complex = $this->wrapper()->getComplexTypes();
            $object = new Object($this->getType(), $this->getName(), $complex);
        } elseif ($this->isComplex()) {
            $complex = $this->complexTypes();
            $object = new Object($this->getType(), $this->getName(), $complex);
        }
        return new Arrays($this->getType(), $this->getName(), $object);
    }

    public function getType()
    {
        return $this->_type;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function complexTypes()
    {
        if (!$this->isComplex()) {
            throw new ParameterParserException("This parameter is not complex type.");
        }
        preg_match("#(@.+)#", $this->_parameter, $complexTypes);
        return ComplexTypeParser::create($complexTypes[1]);
    }

    public function wrapper()
    {
        if (!$this->isComplex()) {
            throw new ParameterParserException("This parameter is not complex type.");
        }
        preg_match('#@className=(.*?)(?:\s|$)#', $this->_parameter, $matches);
        $className = $matches[1];
        $this->_type = str_replace('\\', '', $className);
        $wrapperParser = new WrapperParser($className);
        $wrapperParser->parse();
        return $wrapperParser;
    }

    public function isComplex()
    {
        return in_array($this->getType(), array('object', 'wrapper'));
    }

    /**
     * @param array $parameters
     * @param $methodName
     * @return ParameterParser[]
     */
    public static function create($parameters, $methodName)
    {
        $obj = array();
        foreach ($parameters as $parameter) {
            $parser = new self($parameter, $methodName);
            $obj[] = $parser->parse();
        }
        return $obj;
    }
}
