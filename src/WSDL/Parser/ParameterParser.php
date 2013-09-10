<?php
/**
 * ParameterParser
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Parser;

use WSDL\Types\Object;
use WSDL\Types\Simple;

class ParameterParser
{
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

        switch ($this->getType()) {
            case 'object':
                return new Object($this->getType(), $this->getName(), $this->complexTypes());
                break;
            case 'wrapper':
                $wrapper = $this->wrapper();
                return new Object($this->getType(), $this->getName(), $wrapper->getComplexTypes());
                break;
            default:
                return new Simple($this->getType(), $this->getName());
        }
    }

    private function _parseAndSetType()
    {
        preg_match('#(\w+)#', $this->_parameter, $type);
        $this->_type = $type[1];
    }

    private function _parseAndSetName()
    {
        preg_match('#\\$(\w+)#', $this->_parameter, $name);
        $this->_name = isset($name[1]) ? $name[1] : '';
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
        preg_match('#@className=(.+)#', $this->_parameter, $matches);
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