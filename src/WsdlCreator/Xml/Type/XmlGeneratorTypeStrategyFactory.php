<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Xml\Type;

use InvalidArgumentException;
use WsdlCreator\Annotation\SOAPBindingStyle;
use WsdlCreator\Xml\XmlClassModeler;

/**
 * @author Piotr Olaszewski
 */
class XmlGeneratorTypeStrategyFactory
{
    public function __construct(private XmlClassModeler $xmlClassModeler)
    {
    }

    public function create(string $style): XmlGeneratorTypeStrategy
    {
        return match ($style) {
            SOAPBindingStyle::DOCUMENT => new XmlGeneratorDocumentTypeStrategy($this->xmlClassModeler),
            SOAPBindingStyle::RPC => new XmlGeneratorRpcTypeStrategy($this->xmlClassModeler),
            default => throw new InvalidArgumentException("Style '{$style}' is not exists")
        };
    }
}
