<?php
namespace WSDL\XML;

use DOMDocument;
use WSDL\Utilities\XMLAttributeHelper;

class XMLLiteralUseStrategy implements XMLUseStrategy
{
    public function generate(DOMDocument $DOMDocument, $targetNamespace)
    {
        return XMLAttributeHelper::forDOM($DOMDocument)
            ->createElementWithAttributes('soap:body', array(
                'use' => 'literal',
                'namespace' => $targetNamespace
            ));

    }
}
