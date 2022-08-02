<?php

namespace AssertPhp\Api;

use Throwable;

class ThrowableAssert extends Assert
{
    /** @var Throwable */
    protected mixed $actual;

    public function __construct(Throwable $throwable)
    {
        parent::__construct($throwable);
    }

    public function isInstanceOf(string $class): static
    {
        \PHPUnit\Framework\Assert::assertInstanceOf($class, $this->actual);
        return $this;
    }

    public function hasMessage(string $message): static
    {
        \PHPUnit\Framework\Assert::assertSame($message, $this->actual->getMessage());
        return $this;
    }
}
