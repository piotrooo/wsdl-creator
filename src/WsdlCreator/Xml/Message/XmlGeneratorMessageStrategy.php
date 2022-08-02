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
interface XmlGeneratorMessageStrategy
{
    public function generate(Service $service, DOMDocument $wsdlDocument, DOMElement $definitionsElement): void;
}
