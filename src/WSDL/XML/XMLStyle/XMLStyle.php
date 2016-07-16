<?php
namespace WSDL\XML\XMLStyle;

use DOMDocument;
use DOMElement;
use WSDL\Annotation\SoapBinding;
use WSDL\Parser\Node;

class XMLStyle
{
    /**
     * @var XMLStyleStrategy
     */
    private $strategy;

    public function __construct($style)
    {
        switch ($style) {
            case SoapBinding::RPC:
                $this->strategy = new XMLRpcStyleStrategy();
                break;
        }
    }

    /**
     * @param DOMDocument $DOMDocument
     * @return DOMElement
     */
    public function getBindingDOMDocument(DOMDocument $DOMDocument)
    {
        return $this->strategy->generateBinding($DOMDocument);
    }

    /**
     * @param DOMDocument $DOMDocument
     * @param Node[] $nodes
     * @return DOMElement[]
     */
    public function getMessagePartDOMDocument(DOMDocument $DOMDocument, $nodes)
    {
        return $this->strategy->generateMessagePart($DOMDocument, $nodes);
    }
}
