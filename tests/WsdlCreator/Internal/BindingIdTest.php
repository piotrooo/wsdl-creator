<?php
namespace WsdlCreator\Internal;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WsdlCreator\Annotation\Binding;
use WsdlCreator\Annotation\BindingType;

/**
 * @author Piotr Olaszewski
 */
class BindingIdTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnDefaultValueWhenBindingTypeAnnotationIsNotSet(): void
    {
        //given
        $class = new class {
        };

        //when
        $bindingId = BindingId::parse(new ReflectionClass($class));

        //then
        assertThatString($bindingId)->isEqualTo(Binding::SOAP11);
    }

    /**
     * @test
     */
    public function shouldReturnDefaultValueWhenBindingTypeExistsButNotSetAnything(): void
    {
        //when
        $bindingId = BindingId::parse(new ReflectionClass(ClassWithDefaultBindingType::class));

        //then
        assertThatString($bindingId)->isEqualTo(Binding::SOAP11);
    }

    /**
     * @test
     */
    public function shouldReturnValueFromBindingType(): void
    {
        //when
        $bindingId = BindingId::parse(new ReflectionClass(ClassWithBindingType::class));

        //then
        assertThatString($bindingId)->isEqualTo(Binding::SOAP12);
    }
}

#[BindingType]
class ClassWithDefaultBindingType
{
}

#[BindingType(Binding::SOAP12)]
class ClassWithBindingType
{
}
