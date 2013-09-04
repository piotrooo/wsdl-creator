<?php
/**
 * MethodParser
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Parser;

class MethodParser
{
    private $_name;
    private $_doc;

    public function __construct($name, $doc)
    {
        $this->_name = $name;
        $this->_doc = $doc;
    }

    public function description()
    {
        preg_match('#@desc(.+)#', $this->_doc, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches);
        return !empty($trimGroupMatches[1]) ? $trimGroupMatches[1] : '';
    }

    /**
     * @return ParameterParser[]
     */
    public function parameters()
    {
        preg_match_all('#@param(.+)#', $this->_doc, $groupMatches);
        return ParameterParser::create($groupMatches[1]);
    }

    public function returning()
    {
        preg_match('#@return(.+)#', $this->_doc, $groupMatches);
        $trimGroupMatches = array_map('trim', $groupMatches);
        return new ParameterParser($trimGroupMatches[1]);
    }

    public function getDoc()
    {
        return $this->_doc;
    }

    public function getName()
    {
        return $this->_name;
    }
}