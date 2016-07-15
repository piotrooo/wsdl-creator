<?php
namespace WSDL\XML;

use DOMDocument;

interface XMLUseStrategy
{
    public function generate(DOMDocument $DOMDocument, $targetNamespace);
}
