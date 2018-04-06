<?php
/**
 * Copyright (C) 2013-2018
 * Piotr Olaszewski <piotroo89@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Tests\WSDL\Builder;

use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use PHPUnit_Framework_TestCase;
use WSDL\Annotation\BindingType;
use WSDL\Annotation\SoapBinding;
use WSDL\Builder\AnnotationWSDLBuilder;

/**
 * AnnotationWSDLBuilderTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class AnnotationWSDLBuilderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateBuilderForWebServiceAnnotation()
    {
        //given
        $annotationWSDLBuilder = new AnnotationWSDLBuilder('\Fixtures\ServiceAllAnnotations');

        //when
        $annotationWSDLBuilder->build();

        //then
        $WSDLBuilder = $annotationWSDLBuilder->getBuilder();
        $this->assertEquals('WebServiceAnnotations', $WSDLBuilder->getName());
        $this->assertEquals('http://foo.bar/webserviceannotations', $WSDLBuilder->getTargetNamespace());
        $this->assertEquals('http://foo.bar/webserviceannotations/types', $WSDLBuilder->getNs());
        $this->assertEquals('http://localhost/wsdl-creator/service.php', $WSDLBuilder->getLocation());
    }

    /**
     * @test
     */
    public function shouldCreateBuilderForBindingTypeAnnotation()
    {
        //given
        $annotationWSDLBuilder = new AnnotationWSDLBuilder('\Fixtures\ServiceAllAnnotations');

        //when
        $annotationWSDLBuilder->build();

        //then
        $WSDLBuilder = $annotationWSDLBuilder->getBuilder();
        $this->assertEquals(BindingType::SOAP_12, $WSDLBuilder->getSoapVersion());
    }

    /**
     * @test
     */
    public function shouldCreateBuilderForSoapBindingAnnotation()
    {
        //given
        $annotationWSDLBuilder = new AnnotationWSDLBuilder('\Fixtures\ServiceAllAnnotations');

        //when
        $annotationWSDLBuilder->build();

        //then
        $WSDLBuilder = $annotationWSDLBuilder->getBuilder();
        $this->assertEquals(SoapBinding::DOCUMENT, $WSDLBuilder->getStyle());
        $this->assertEquals(SoapBinding::ENCODED, $WSDLBuilder->getUse());
        $this->assertEquals(SoapBinding::WRAPPED, $WSDLBuilder->getParameterStyle());
    }

    /**
     * @test
     */
    public function shouldCreateBuilderWithClassNameWhenNameIsNotPass()
    {
        //given
        $annotationWSDLBuilder = new AnnotationWSDLBuilder('\Fixtures\ServiceClassNameAnnotations');

        //when
        $annotationWSDLBuilder->build();

        //then
        $WSDLBuilder = $annotationWSDLBuilder->getBuilder();
        $this->assertEquals('ServiceClassNameAnnotations', $WSDLBuilder->getName());
    }

    /**
     * @test
     */
    public function shouldCreateBuilderWithMethods()
    {
        //given
        $annotationWSDLBuilder = new AnnotationWSDLBuilder('\Fixtures\ServiceAllAnnotations');

        //when
        $annotationWSDLBuilder->build();

        //then
        $WSDLBuilder = $annotationWSDLBuilder->getBuilder();
        Assert::thatArray($WSDLBuilder->getMethods())
            ->extracting('name')
            ->containsOnly('uppercaseUserName', 'appendPrefixToNumbers', 'getUserContext');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenWebServiceAnnotationIsNotSet()
    {
        //given
        $annotationWSDLBuilder = new AnnotationWSDLBuilder('\Fixtures\ServiceWithoutWebServiceAnnotation');

        //when
        CatchException::when($annotationWSDLBuilder)->build();

        //then
        CatchException::assertThat()
            ->isInstanceOf('\WSDL\Builder\AnnotationBuilderException')
            ->hasMessage('Class must have @WebService annotation');
    }

    /**
     * @test
     */
    public function shouldCreateBuilderWithWebMethodsOnly()
    {
        //given
        $annotationWSDLBuilder = new AnnotationWSDLBuilder('\Fixtures\ServiceWebMethods');

        //when
        $annotationWSDLBuilder->build();

        //then
        $WSDLBuilder = $annotationWSDLBuilder->getBuilder();
        Assert::thatArray($WSDLBuilder->getMethods())
            ->extracting('name')
            ->containsOnly('uppercaseUserName', 'methodWithoutParameters', 'methodWithoutResult');
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenClassNotExists()
    {
        //given
        $annotationWSDLBuilder = new AnnotationWSDLBuilder('\Non\Exists\Class');

        //when
        CatchException::when($annotationWSDLBuilder)->build();

        //then
        CatchException::assertThat()->hasMessage('Class [\Non\Exists\Class] not exists');
    }
}
