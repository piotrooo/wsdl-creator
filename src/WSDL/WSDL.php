<?php
namespace WSDL;

use WSDL\Builder\WSDLBuilder;
use WSDL\XML\XMLProvider;

class WSDL
{
    private $xml;

    public function __construct($xml)
    {
        $this->xml = $xml;
    }

    public function render()
    {
        header("Content-Type: text/xml");
        echo $this->xml;
    }

    public static function fromBuilder(WSDLBuilder $builder)
    {
        $xml = new XMLProvider($builder);
        $xml->generate();
        return new self($xml->getXml());
    }
}
