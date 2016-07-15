<?php
namespace WSDL\XML;

use DOMDocument;

interface XMLStyleStrategy
{
    public function generateBinding(DOMDocument $DOMDocument);

    public function generateMessagePart(DOMDocument $DOMDocument, $nodes);
}
