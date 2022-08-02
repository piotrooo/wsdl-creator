<?php

namespace AssertPhp;

use AssertPhp\Api\StringAssert;
use AssertPhp\Api\ThrowableAssert;
use PHPUnit\Framework\ExpectationFailedException;
use Throwable;

class Assertions
{
    private function __construct()
    {
    }

    public static function thatThrownBy(callable $shouldRaiseThrowable): ThrowableAssert
    {
        try {
            $shouldRaiseThrowable();
        } catch (Throwable $throwable) {
            return new ThrowableAssert($throwable);
        }

        throw new ExpectationFailedException('Expecting code to raise a throwable.');
    }

    public static function thatString(string $actual): StringAssert
    {
        return new StringAssert($actual);
    }
}
