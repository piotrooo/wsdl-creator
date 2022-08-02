<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Xml\Type;

use DOMDocument;
use DOMElement;
use WsdlCreator\Internal\Model\Service;

/**
 * @author Piotr Olaszewski
 */
interface XmlGeneratorTypeStrategy
{
    public function generate(Service $service, DOMDocument $wsdlDocument, DOMElement $xsSchemaElement): void;
}
