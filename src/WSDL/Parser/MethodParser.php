<?php
/**
 * MethodParser
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Parser;

use WSDL\Types\Type;
use WSDL\XML\SOAPGenerator;

class MethodParser
{
    private $_name;
    private $_doc;
    private $_rawParameters;
    private $_rawReturn;

    public function __construct($name, $doc)
    {
        $this->_name = $name;
        $this->_doc = $doc;
    }

    public function description()
    {
        preg_match('#@desc (.+)#', $this->_doc, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches);
        return !empty($trimGroupMatches[1]) ? $trimGroupMatches[1] : '';
    }

    /**
     * @return Type[]
     */
    public function parameters()
    {
        preg_match_all('#@param (.+)#', $this->_doc, $groupMatches);
        $this->_rawParameters = $groupMatches[1];
        return ParameterParser::create($groupMatches[1], $this->getName());
    }

    public function returning()
    {
        preg_match('#@return (.+)#', $this->_doc, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches);
        if (isset($trimGroupMatches[1])) {
            $this->_rawReturn = $trimGroupMatches[1];
        }
        $parameterParser = new ParameterParser($this->_rawReturn, $this->getName());
        return $parameterParser->parse();
    }

    public function getDoc()
    {
        return $this->_doc;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getRawParameters()
    {
        $this->parameters();
        return $this->_rawParameters;
    }

    public function getRawReturn()
    {
        $this->returning();
        return $this->_rawReturn;
    }

    public function getMethodSampleRequest($url)
    {
        return new SOAPGenerator($this->parameters(), $this->getName(), $url);
    }
}