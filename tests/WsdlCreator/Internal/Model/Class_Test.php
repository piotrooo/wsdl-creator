<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Internal\Model;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use WsdlCreator\Annotation\SOAPBinding;
use WsdlCreator\Annotation\WebService;

/**
 * @author Piotr Olaszewski
 */
class Class_Test extends TestCase
{
    /**
     * @test
     */
    public function shouldGetDefaultName(): void
    {
        //given
        $class = new Class_(new ReflectionClass(SampleForClass_::class), new WebService(), new SOAPBinding());

        //when
        $name = $class->getName();

        //then
        assertThatString($name)->isEqualTo('SampleForClass_');
    }

    /**
     * @test
     */
    public function shouldGetName(): void
    {
        //given
        $class = new Class_(new ReflectionClass(SampleForClass_::class), new WebService(name: 'some-name'), new SOAPBinding());

        //when
        $name = $class->getName();

        //then
        assertThatString($name)->isEqualTo('some-name');
    }

    /**
     * @test
     */
    public function shouldGetDefaultTargetNamespace(): void
    {
        //given
        $class = new Class_(new ReflectionClass(SampleForClass_::class), new WebService(), new SOAPBinding());

        //when
        $targetNamespace = $class->getTargetNamespace();

        //then
        assertThatString($targetNamespace)->isEqualTo('urn:sampleforclass_.model.internal.wsdlcreator');
    }

    /**
     * @test
     */
    public function shouldGetTargetNamespace(): void
    {
        //given
        $class = new Class_(new ReflectionClass(SampleForClass_::class), new WebService(targetNamespace: 'http://127.0.0.1/service'), new SOAPBinding());

        //when
        $targetNamespace = $class->getTargetNamespace();

        //then
        assertThatString($targetNamespace)->isEqualTo('http://127.0.0.1/service');
    }

    /**
     * @test
     */
    public function shouldGetSanitizedTargetNamespace(): void
    {
        //given
        $class = new Class_(new ReflectionClass(SampleForClass_::class), new WebService(targetNamespace: 'my-service'), new SOAPBinding());

        //when
        $targetNamespace = $class->getTargetNamespace();

        //then
        assertThatString($targetNamespace)->isEqualTo('urn:my-service');
    }

    /**
     * @test
     */
    public function shouldGetDefaultServiceName(): void
    {
        //given
        $class = new Class_(new ReflectionClass(SampleForClass_::class), new WebService(), new SOAPBinding());

        //when
        $serviceName = $class->getServiceName();

        //then
        assertThatString($serviceName)->isEqualTo('SampleForClass_Service');
    }

    /**
     * @test
     */
    public function shouldGetServiceName(): void
    {
        //given
        $class = new Class_(new ReflectionClass(SampleForClass_::class), new WebService(serviceName: 'my-service'), new SOAPBinding());

        //when
        $serviceName = $class->getServiceName();

        //then
        assertThatString($serviceName)->isEqualTo('my-service');
    }

    /**
     * @test
     */
    public function shouldGetDefaultPortName(): void
    {
        //given
        $class = new Class_(new ReflectionClass(SampleForClass_::class), new WebService(), new SOAPBinding());

        //when
        $portName = $class->getPortName();

        //then
        assertThatString($portName)->isEqualTo('SampleForClass_Port');
    }

    /**
     * @test
     */
    public function shouldGetPortName(): void
    {
        //given
        $class = new Class_(new ReflectionClass(SampleForClass_::class), new WebService(portName: 'my-service'), new SOAPBinding());

        //when
        $portName = $class->getPortName();

        //then
        assertThatString($portName)->isEqualTo('my-service');
    }
}

class SampleForClass_
{
}
