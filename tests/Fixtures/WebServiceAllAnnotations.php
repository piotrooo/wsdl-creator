<?php
/**
 * Copyright (C) 2013-2016
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
namespace Fixtures;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use stdClass;
use WSDL\Annotation\BindingType;
use WSDL\Annotation\SoapBinding;
use WSDL\Annotation\WebMethod;
use WSDL\Annotation\WebParam;
use WSDL\Annotation\WebResult;
use WSDL\Annotation\WebService;

/**
 * WebServiceAnnotations
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 *
 * @WebService(
 *     name="WebServiceAnnotations",
 *     targetNamespace="http://foo.bar/webserviceannotations",
 *     location="http://localhost/wsdl-creator/service.php",
 *     ns="http://foo.bar/webserviceannotations/types"
 * )
 * @BindingType(value="SOAP_12")
 * @SoapBinding(style="DOCUMENT", use="LITERAL", parameterStyle="WRAPPED")
 */
class WebServiceAllAnnotations
{
    /**
     * @WebMethod()
     * @WebParam(param="string $userName")
     * @WebResult(param="string $uppercasedUserName")
     */
    public function uppercaseUserName($userName)
    {
        return strtoupper($userName);
    }

    /**
     * @WebMethod()
     * @WebParam(param="int[] $numbers")
     * @WebParam(param="string $prefix")
     * @WebResult(param="string[] $numbersWithPrefix")
     */
    public function appendPrefixToNumbers($numbers, $prefix)
    {
        return Arrays::map($numbers, Functions::prepend($prefix));
    }

    /**
     * @WebMethod()
     * @WebParam(param="string $token", header=true)
     * @WebParam(param="object $user { string $name int $age }")
     * @WebResult(param="object $userContext { string $token int $id object $userInfo { string $name int $age } }")
     */
    public function getUserContext($token, $user)
    {
        $userContext = new stdClass();
        $userContext->token = $token;
        $userContext->id = time();
        $userContext->UserInfo = new stdClass();
        $userContext->UserInfo->name = $user->name;
        $userContext->UserInfo->age = $user->age;
        return $userContext;
    }
}
