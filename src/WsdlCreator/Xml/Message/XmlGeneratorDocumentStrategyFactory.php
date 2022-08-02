<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Xml\Message;

use InvalidArgumentException;
use WsdlCreator\Annotation\SOAPBindingStyle;

/**
 * @author Piotr Olaszewski
 */
class XmlGeneratorDocumentStrategyFactory
{
    public function create(string $style): XmlGeneratorMessageStrategy
    {
        return match ($style) {
            SOAPBindingStyle::DOCUMENT => new XmlGeneratorDocumentMessageStrategy(),
            SOAPBindingStyle::RPC => new XmlGeneratorRpcMessageStrategy(),
            default => throw new InvalidArgumentException("Style '{$style}' is not exists")
        };
    }
}
