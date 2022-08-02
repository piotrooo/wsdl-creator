<?php
namespace WsdlCreator\Internal;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use WsdlCreator\Annotation\WebMethod;
use WsdlCreator\Annotation\WebService;

/**
 * @author Piotr Olaszewski
 */
class ServiceModelFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldThrowExceptionWhenClassHasNotAnnotationWebService(): void
    {
        //given
        $factory = new ServiceModelFactory();
        $reflectionClass = new ReflectionClass(ClassWithoutWebService::class);

        //when/then
        assertThatThrownBy(fn() => $factory->create($reflectionClass))
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('ClassWithoutWebService has not #[WebService] attribute');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenWebServiceNotHaveAnyValidMethods(): void
    {
        //given
        $factory = new ServiceModelFactory();
        $reflectionClass = new ReflectionClass(ClassWithoutWebMethods::class);

        //when/then
        assertThatThrownBy(fn() => $factory->create($reflectionClass))
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('The web service defined by class ClassWithoutWebMethods does not contains any valid web methods');
    }
}

class ClassWithoutWebService
{
}

#[WebService]
class ClassWithoutWebMethods
{
    #[WebMethod(exclude: true)]
    public function publicExcludedFunction(): void
    {
    }

    protected function protectedFunction(): void
    {
    }

    private function privateFunction(): void
    {
    }
}
