<?php
namespace WSDL\XML;

use DOMDocument;
use WSDL\Annotation\SoapBinding;

class XMLUse
{
    private $strategy;

    public function __construct($use)
    {
        switch ($use) {
            case SoapBinding::LITERAL:
                $this->strategy = new XMLLiteralUseStrategy();
                break;
            case SoapBinding::ENCODED:
                break;
        }
    }

    public function getDOMDocument(DOMDocument $DOMDocument, $targetNamespace)
    {
        return $this->strategy->generate($DOMDocument, $targetNamespace);
    }
}
