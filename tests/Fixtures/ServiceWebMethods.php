<?php
/**
 * Copyright (C) 2013-2019
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

use WSDL\Annotation\WebMethod;
use WSDL\Annotation\WebParam;
use WSDL\Annotation\WebResult;
use WSDL\Annotation\WebService;

/**
 * ServiceWebMethods
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 *
 * @WebService(
 *     targetNamespace="http://foo.bar/servicewithnotwebmethods",
 *     location="http://localhost/wsdl-creator/service.php",
 *     ns="http://foo.bar/servicewithnotwebmethods/types"
 * )
 */
class ServiceWebMethods
{
    /**
     * @WebMethod
     * @WebParam(param="string $userName")
     * @WebResult(param="string $uppercasedUserName")
     */
    public function uppercaseUserName($userName)
    {
        return strtoupper($userName);
    }

    public function toLog($message)
    {
    }

    /**
     * @WebMethod
     * @WebResult(param="string $uniqueId")
     */
    public function methodWithoutParameters()
    {
        return uniqid();
    }

    /**
     * @WebMethod(operationName="emptyResult")
     */
    public function methodWithoutResult()
    {
    }
}
