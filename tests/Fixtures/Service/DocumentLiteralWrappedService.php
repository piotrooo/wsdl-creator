<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace Fixtures\Service;

use Fixtures\Service\Model\Person;
use Fixtures\Service\Model\Result;
use WsdlCreator\Annotation\WebService;

#[WebService]
class DocumentLiteralWrappedService
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
}