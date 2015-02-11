<?php
/**
 * MethodWrapper
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\Service;

use DOMDocument;

class MethodWrapper
{
    public $name;
    public $parameters;
    public $return;
    public $sampleRequest;

    public function __construct($name, $parameters, $return, $sampleRequest)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->return = $return;
        $this->sampleRequest = $sampleRequest;
    }

    public function getSampleRequest()
    {
        $DOMDocument = new DOMDocument();
        $DOMDocument->loadXML($this->sampleRequest);
        $DOMDocument->formatOutput = true;
        return $DOMDocument->saveXML();
    }
}
