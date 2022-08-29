<?php

namespace WsdlCreator\Internal\Model;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WsdlCreator\Annotation\WebMethod;

class MethodTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetDefaultOperationName(): void
    {
        //given
        $reflectionClass = new ReflectionClass(SampleForMethod::class);
        $reflectionMethod = $reflectionClass->getMethod('method');
        $method = new Method($reflectionMethod, new WebMethod(), [], new MethodReturn($reflectionMethod->getReturnType(), null));

        //when
        $operationName = $method->getOperationName();

        //then
        assertThatString($operationName)->isEqualTo('method');
    }

    /**
     * @test
     */
    public function shouldGetOperationName(): void
    {
        //given
        $reflectionClass = new ReflectionClass(SampleForMethod::class);
        $reflectionMethod = $reflectionClass->getMethod('method');
        $method = new Method($reflectionMethod, new WebMethod(operationName: 'some-operation'), [], new MethodReturn($reflectionMethod->getReturnType(), null));

        //when
        $operationName = $method->getOperationName();

        //then
        assertThatString($operationName)->isEqualTo('some-operation');
    }
}

class SampleForMethod
{
    public function method(): string
    {
        return '';
    }
}
