<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Xml\Message;

use DOMDocument;
use DOMElement;
use WsdlCreator\Internal\Model\Service;

/**
 * @author Piotr Olaszewski
 */
class XmlGeneratorDocumentMessageStrategy implements XmlGeneratorMessageStrategy
{
    public function generate(Service $service, DOMDocument $wsdlDocument, DOMElement $definitionsElement): void
    {
        $methods = $service->getMethods();
        foreach ($methods as $method) {
            $name = $method->getOperationName();

            $messageElement = $wsdlDocument->createElement('message');
            $messageElement->setAttribute('name', $name);
            $definitionsElement->appendChild($messageElement);

            $partElement = $wsdlDocument->createElement('part');
            $partElement->setAttribute('name', 'parameters');
            $partElement->setAttribute('element', "tns:{$name}");
            $messageElement->appendChild($partElement);

            $messageResponseElement = $wsdlDocument->createElement('message');
            $messageResponseElement->setAttribute('name', "{$name}Response");
            $definitionsElement->appendChild($messageResponseElement);

            $partResponseElement = $wsdlDocument->createElement('part');
            $partResponseElement->setAttribute('name', 'parameters');
            $partResponseElement->setAttribute('element', "tns:{$name}Response");
            $messageResponseElement->appendChild($partResponseElement);
        }
    }
}
