<?php
/**
 * ParameterParser
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Parser;

class ParameterParser
{
    private $_parameter;
    private $_type;
    private $_name;

    public function __construct($parameter)
    {
        $this->_parameter = trim($parameter);
        $this->_parse();
    }

    public function getType()
    {
        return $this->_type;
    }

    public function getName()
    {
        return $this->_name;
    }

    private function _parse()
    {
        $this->_parseAndSetType();
        $this->_parseAndSetName();
    }

    private function _parseAndSetType()
    {
        preg_match('#(\w*\[?\]?)#', $this->_parameter, $type);
        if ($this->_isArray($type[1])) {

        } else {
            $this->_type = $type[1];
        }
    }

    private function _isArray($type)
    {
        return preg_match('#\[\]#', $type);
    }

    private function _parseAndSetName()
    {
        preg_match('#\\$(\w+)#', $this->_parameter, $name);
        $this->_name = isset($name[1]) ? $name[1] : '';
    }

    public function isComplex()
    {
        return in_array($this->getType(), array('object', 'wrapper', 'array'));
    }

    /**
     * @return ComplexTypeParser[]
     * @throws ParameterParserException
     */
    public function complexTypes()
    {
        if (!$this->isComplex()) {
            throw new ParameterParserException("This parameter is not complex type.");
        }
        preg_match("#@(.+)#", $this->_parameter, $complexTypes);
        return ComplexTypeParser::create($complexTypes[1]);
    }

    /**
     * @param array $parameters
     * @return ParameterParser[]
     */
    public static function create($parameters)
    {
        $obj = array();
        foreach ($parameters as $parameter) {
            $obj[] = new self($parameter);
        }
        return $obj;
    }
}