<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace Fixtures\Service;

use Fixtures\Service\Model\Person;
use Fixtures\Service\Model\Result;
use Fixtures\Service\Model\Token;
use WsdlCreator\Annotation\SOAPBinding;
use WsdlCreator\Annotation\SOAPBindingParameterStyle;
use WsdlCreator\Annotation\SOAPBindingStyle;
use WsdlCreator\Annotation\SOAPBindingUse;
use WsdlCreator\Annotation\WebMethod;
use WsdlCreator\Annotation\WebParam;
use WsdlCreator\Annotation\WebService;

/**
 * @author Piotr Olaszewski
 */
#[WebService]
#[SOAPBinding(style: SOAPBindingStyle::RPC, use: SOAPBindingUse::LITERAL, parameterStyle: SOAPBindingParameterStyle::WRAPPED)]
class RpcLiteralWrappedService
{
    public function sampleMethod(string $value): string
    {
        return "sampleMethod response";
    }

    /**
     * @param string[] $names
     * @return string[]
     */
    public function append(array $names): array
    {
        return $names;
    }

    public function put(Person $person, string $value): Result
    {
        return (new Result())
            ->setPerson($person)
            ->setValue($value);
    }

    #[WebMethod(operationName: 'set-person')]
    public function set(Person $person): void
    {
    }

    /**
     * @param Person[] $persons
     * @return Result[]
     */
    public function setAll(array $persons): array
    {
        $object = [];
        foreach ($persons as $i => $person) {
            $object[] = (new Result())
                ->setPerson($person)
                ->setValue("value:{$i}");
        }
        return $object;
    }

    #[WebMethod(action: 'sum-action')]
    public function add(#[WebParam(name: 'first-param')] int $a, #[WebParam(name: 'second-param', partName: 'b-part-name')] int $b): void
    {
    }

    public function operationWithHeader(#[WebParam(header: true)] string $token, #[WebParam(name: 'anotherToken', header: true)] Token $anotherToken): string
    {
        return '';
    }
}
