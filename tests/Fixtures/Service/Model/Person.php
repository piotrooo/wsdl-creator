<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace Fixtures\Service\Model;

class Person
{
    private string $stringType;
    private int $intType;
    private bool $boolType;
    private float $floatType;

    private ?string $stringOrNullType;
    private ?int $intOrNullType;
    private ?bool $boolOrNullType;
    private ?float $floatOrNullType;

    /**
     * @var string[]
     */
    private array $arrayOfScalars;
    /**
     * @var string[]|null
     */
    private ?array $arrayOfScalarsOrNullType;
    /**
     * @var Tag[]
     */
    private array $arrayOfObjects;
    /**
     * @var Tag[]|null
     */
    private ?array $arrayOfObjectsOrNullType;

    private Tag $object;

    private ?Tag $objectOrNullType;

    public function getStringType(): string
    {
        return $this->stringType;
    }

    public function setStringType(string $stringType): void
    {
        $this->stringType = $stringType;
    }

    public function getIntType(): int
    {
        return $this->intType;
    }

    public function setIntType(int $intType): void
    {
        $this->intType = $intType;
    }

    public function isBoolType(): bool
    {
        return $this->boolType;
    }

    public function setBoolType(bool $boolType): void
    {
        $this->boolType = $boolType;
    }

    public function getFloatType(): float
    {
        return $this->floatType;
    }

    public function setFloatType(float $floatType): void
    {
        $this->floatType = $floatType;
    }

    public function getStringOrNullType(): ?string
    {
        return $this->stringOrNullType;
    }

    public function setStringOrNullType(?string $stringOrNullType): void
    {
        $this->stringOrNullType = $stringOrNullType;
    }

    public function getIntOrNullType(): ?int
    {
        return $this->intOrNullType;
    }

    public function setIntOrNullType(?int $intOrNullType): void
    {
        $this->intOrNullType = $intOrNullType;
    }

    public function getBoolOrNullType(): ?bool
    {
        return $this->boolOrNullType;
    }

    public function setBoolOrNullType(?bool $boolOrNullType): void
    {
        $this->boolOrNullType = $boolOrNullType;
    }

    public function getFloatOrNullType(): ?float
    {
        return $this->floatOrNullType;
    }

    public function setFloatOrNullType(?float $floatOrNullType): void
    {
        $this->floatOrNullType = $floatOrNullType;
    }

    /**
     * @return string[]
     */
    public function getArrayOfScalars(): array
    {
        return $this->arrayOfScalars;
    }

    /**
     * @param string[] $arrayOfScalars
     */
    public function setArrayOfScalars(array $arrayOfScalars): void
    {
        $this->arrayOfScalars = $arrayOfScalars;
    }

    /**
     * @return string[]|null
     */
    public function getArrayOfScalarsOrNullType(): ?array
    {
        return $this->arrayOfScalarsOrNullType;
    }

    /**
     * @param string[]|null $arrayOfScalarsOrNullType
     */
    public function setArrayOfScalarsOrNullType(?array $arrayOfScalarsOrNullType): void
    {
        $this->arrayOfScalarsOrNullType = $arrayOfScalarsOrNullType;
    }

    /**
     * @return Tag[]
     */
    public function getArrayOfObjects(): array
    {
        return $this->arrayOfObjects;
    }

    /**
     * @param Tag[] $arrayOfObjects
     */
    public function setArrayOfObjects(array $arrayOfObjects): void
    {
        $this->arrayOfObjects = $arrayOfObjects;
    }

    /**
     * @return Tag[]|null
     */
    public function getArrayOfObjectsOrNullType(): ?array
    {
        return $this->arrayOfObjectsOrNullType;
    }

    /**
     * @param Tag[]|null $arrayOfObjectsOrNullType
     */
    public function setArrayOfObjectsOrNullType(?array $arrayOfObjectsOrNullType): void
    {
        $this->arrayOfObjectsOrNullType = $arrayOfObjectsOrNullType;
    }

    public function getObject(): Tag
    {
        return $this->object;
    }

    public function setObject(Tag $object): void
    {
        $this->object = $object;
    }

    public function getObjectOrNullType(): ?Tag
    {
        return $this->objectOrNullType;
    }

    public function setObjectOrNullType(?Tag $objectOrNullType): void
    {
        $this->objectOrNullType = $objectOrNullType;
    }
}
