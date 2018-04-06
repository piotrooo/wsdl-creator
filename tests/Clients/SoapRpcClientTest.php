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
namespace Clients;

use Fixtures\WsdlProvider;
use Ouzo\Tests\Assert;
use PHPUnit_Framework_TestCase;
use SoapClient;
use SoapHeader;
use stdClass;

/**
 * SoapRpcClientTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class SoapRpcClientTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SoapClient
     */
    private $soapClient;

    protected function setUp()
    {
        parent::setUp();
        $this->markTestSkipped('Integration tests');
        $this->soapClient = new SoapClient(WsdlProvider::rpcLiteral(), [
            'cache_wsdl' => WSDL_CACHE_NONE,
            'trace' => true
        ]);
    }

    /**
     * @test
     */
    public function shouldHandleMethodUppercaseUserName()
    {
        //when
        $uppercasedName = $this->soapClient->uppercaseUserName('Piotr');

        //then
        $this->assertEquals('PIOTR', $uppercasedName);
    }

    /**
     * @test
     */
    public function shouldHandleMethodAppendPrefixToNumbers()
    {
        //given
        $numbers = [1, 2, 3];

        //when
        $numbersWithPrefix = $this->soapClient->appendPrefixToNumbers($numbers, 'PREF - ');

        //then
        Assert::thatArray($numbersWithPrefix)->containsExactly('PREF - 1', 'PREF - 2', 'PREF - 3');
    }

    /**
     * @test
     */
    public function shouldHandleMethodGetUserContext()
    {
        //given
        $user = new stdClass();
        $user->name = 'Piotr';
        $user->age = '27';

        //when
        $userContext = $this->soapClient->getUserContext($user);

        //then
        $this->assertNotEmpty($userContext->id);
        $this->assertEquals('Piotr', $userContext->userInfo->name);
        $this->assertEquals(27, $userContext->userInfo->age);
    }

    /**
     * @test
     */
    public function shouldHandleMethodExtractCompaniesNames()
    {
        //given
        $companies = [];
        $companies[0] = new stdClass();
        $companies[0]->name = 'Company 1';
        $companies[0]->postcode = '11-111';
        $companies[1] = new stdClass();
        $companies[1]->name = 'Company 2';
        $companies[1]->postcode = '22-222';

        //when
        $companiesNames = $this->soapClient->extractCompaniesNames($companies);

        //then
        Assert::thatArray($companiesNames)->containsExactly('Company 1', 'Company 2');
    }

    /**
     * @test
     */
    public function shouldHandleMethodWrapErrors()
    {
        //given
        $errors = ['error 1', 'error 2', 'error 3'];

        //when
        $result = $this->soapClient->wrapErrors($errors);

        //then
        $this->assertFalse($result->result);
        Assert::thatArray($result->errors)->containsExactly('error 1', 'error 2', 'error 3');
    }

    /**
     * @test
     */
    public function shouldHandleMethodAuthorizedMethod()
    {
        //given
        $data = new stdClass();
        $data->token = 'test_token';
        $data->id = '31242';
        $this->soapClient->__setSoapHeaders(new SoapHeader('http://foo.bar/rpcliteralservice', 'ServiceAuth', $data));

        //when
        $nameWithSurname = $this->soapClient->authorizedMethod('Piotr', 'Olaszewski');

        //then
        $this->assertEquals('clientId [31242] name [Piotr] surname [Olaszewski]', $nameWithSurname);
    }

    /**
     * @test
     */
    public function shouldHandleMethodMethodWithoutReturn()
    {
        //when
        $this->soapClient->methodWithoutReturn('user-token-342gdg453j');

        //then
        $this->assertXmlStringEqualsXmlString('<?xml version="1.0" encoding="UTF-8"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://foo.bar/rpcliteralservice"><SOAP-ENV:Body><ns1:methodWithoutReturnResponse/></SOAP-ENV:Body></SOAP-ENV:Envelope>', $this->soapClient->__getLastResponse());
    }

    /**
     * @test
     */
    public function shouldHandleMethodMethodWithoutParameters()
    {
        //when
        $responseForMethodWithoutParameters = $this->soapClient->methodWithoutParameters();

        //then
        $this->assertEquals('method without parameters', $responseForMethodWithoutParameters);
    }
}
