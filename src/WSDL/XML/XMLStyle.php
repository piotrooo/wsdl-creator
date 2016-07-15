<?php
namespace WSDL\XML;

use DOMDocument;
use WSDL\Annotation\SoapBinding;

class XMLStyle
{
    private $strategy;

    public function __construct($style)
    {
        switch ($style) {
            case SoapBinding::RPC:
                $this->strategy = new XMLRpcStyleStrategy();
                break;
            case SoapBinding::DOCUMENT:
                break;
        }
    }

    public function getBindingDOMDocument(DOMDocument $DOMDocument)
    {
        return $this->strategy->generateBinding($DOMDocument);
    }

    public function getMessagePartDOMDocument(DOMDocument $DOMDocument, $nodes)
    {
        return $this->strategy->generateMessagePart($DOMDocument, $nodes);
    }
}
