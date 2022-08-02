<?php

namespace AssertPhp\Api;

class StringAssert extends Assert
{
    public function isEqualTo(string $expected): static
    {
        \PHPUnit\Framework\Assert::assertSame($expected, $this->actual);
        return $this;
    }
}
