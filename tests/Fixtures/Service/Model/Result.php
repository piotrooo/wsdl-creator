<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace Fixtures\Service\Model;

class Result
{
    private Person $person;
    private string $value;

    public function getPerson(): Person
    {
        return $this->person;
    }

    public function setPerson(Person $person): Result
    {
        $this->person = $person;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): Result
    {
        $this->value = $value;
        return $this;
    }
}
