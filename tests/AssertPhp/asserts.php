<?php

use AssertPhp\Api\StringAssert;
use AssertPhp\Api\ThrowableAssert;
use AssertPhp\Assertions;

if (!function_exists('assertThatThrownBy')) {
    function assertThatThrownBy(callable $shouldRaiseThrowable): ThrowableAssert
    {
        return Assertions::thatThrownBy($shouldRaiseThrowable);
    }
}

if (!function_exists('assertThatString')) {
    function assertThatString(string $actual): StringAssert
    {
        return Assertions::thatString($actual);
    }
}
