<?php
namespace WSDL\XML;

class SOAPGenerator
{
    private $_types;

    function __construct($types)
    {
        $this->_types = $types;
    }

    public function getXML()
    {
        return '';
    }
}