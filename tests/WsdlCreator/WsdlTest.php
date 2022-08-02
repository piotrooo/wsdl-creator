<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator;

use Fixtures\Service\DocumentLiteralWrappedService;
use Fixtures\Service\RpcLiteralWrappedService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @author Piotr Olaszewski
 */
class WsdlTest extends TestCase
{
    private Wsdl $wsdl;

    public function setUp(): void
    {
        parent::setUp();
        $this->wsdl = new Wsdl();
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenAddressIsNotValid(): void
    {
        //when/then
        assertThatThrownBy(fn() => $this->wsdl->generate('not-valid', '\Non\Exists\Class'))
            ->isInstanceOf(InvalidArgumentException::class)
            ->hasMessage("Address 'not-valid' is not valid");
    }

    /**
     * @test
     */
    public function shouldThrowExceptionWhenClassNotExists(): void
    {
        //when/then
        assertThatThrownBy(fn() => $this->wsdl->generate('http://127.0.0.1/service', '\Non\Exists\Class'))
            ->isInstanceOf(InvalidArgumentException::class)
            ->hasMessage("Class '\\Non\\Exists\\Class' is not exists");
    }

    /**
     * @test
     */
    public function shouldGenerateDocumentLiteralWrappedWsdl(): void
    {
        //when
        $actualWsdl = $this->wsdl->generate('http://127.0.0.1/service', DocumentLiteralWrappedService::class);

        //then
        $path = __DIR__ . '/../Fixtures/generated_wsdl/document_literal_wrapped.wsdl';
        $this->assertXmlStringEqualsXmlFile($path, $actualWsdl);
    }

    /**
     * @test
     */
    public function shouldGenerateRpcLiteralWrappedWsdl(): void
    {
        //when
        $actualWsdl = $this->wsdl->generate('http://127.0.0.1/service', RpcLiteralWrappedService::class);

        //then
        $path = __DIR__ . '/../Fixtures/generated_wsdl/rpc_literal_wrapped.wsdl';
        $this->assertXmlStringEqualsXmlFile($path, $actualWsdl);
    }
}
