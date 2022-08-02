<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use WsdlCreator\Internal\BindingId;
use WsdlCreator\Internal\ServiceModelFactory;
use WsdlCreator\Xml\XmlGenerator;

/**
 * Entrypoint for generating WSDL document for service.
 *
 * @author Piotr Olaszewski
 */
class Wsdl
{
    /**
     * Generates a WSDL definition for specified class at the given address.
     *
     * @param string $address a URI specifying the address and transport/protocol to use.
     * @param string $implementor the endpoint WSDL implementor
     * @return string generated WSDL definition
     */
    public function generate(string $address, string $implementor): string
    {
        $this->validateAddress($address);
        $implementorReflectionClass = $this->validateAndReturnReflectedImplementor($implementor);

        $bindingId = BindingId::parse($implementorReflectionClass);
        $service = (new ServiceModelFactory())
            ->create($implementorReflectionClass);

        return (new XmlGenerator())
            ->generate($address, $bindingId, $service)
            ->saveXML();
    }

    private function validateAddress(string $address): void
    {
        $validAddress = filter_var($address, FILTER_VALIDATE_URL);
        if ($validAddress === false) {
            throw new InvalidArgumentException("Address '{$address}' is not valid");
        }
    }

    private function validateAndReturnReflectedImplementor(string $implementor): ReflectionClass
    {
        try {
            return new ReflectionClass($implementor);
        } catch (ReflectionException $e) {
            throw new InvalidArgumentException("Class '{$implementor}' is not exists", $e->getCode(), $e);
        }
    }
}
