<?php
namespace WSDL\XML;

use DOMDocument;
use WSDL\Utilities\XMLAttributeHelper;

class XMLRpcStyleStrategy implements XMLStyleStrategy
{
    public function generateBinding(DOMDocument $DOMDocument)
    {
        return XMLAttributeHelper::forDOM($DOMDocument)
            ->createElementWithAttributes('soap:binding', array(
                'style' => 'rpc',
                'transport' => 'http://schemas.xmlsoap.org/soap/http'
            ));
    }

    public function generateMessagePart(DOMDocument $DOMDocument, $nodes)
    {
        $parts = array();
        $attributes = array();
        foreach ($nodes as $node) {
            if (!$node->isArray()) {
                $attributes = array(
                    'name' => $node->getSanitizedName(),
                    'type' => 'xsd:' . $node->getType()
                );
            }
            $parts[] = XMLAttributeHelper::forDOM($DOMDocument)->createElementWithAttributes('part', $attributes);
        }
        return $parts;
    }
}
