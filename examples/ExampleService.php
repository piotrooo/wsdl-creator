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
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;

/**
 * ExampleService
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class ExampleService
{
    private $clientId = null;

    public function uppercaseUserName($userName)
    {
        return strtoupper($userName);
    }

    public function appendPrefixToNumbers($numbers, $prefix)
    {
        return Arrays::map($numbers, Functions::prepend($prefix));
    }

    public function getUserContext($user)
    {
        $userContext = new stdClass();
        $userContext->id = time();
        $userContext->userInfo = new stdClass();
        $userContext->userInfo->name = $user->name;
        $userContext->userInfo->age = $user->age;
        return $userContext;
    }

    public function extractCompaniesNames($companies)
    {
        return Arrays::map($companies, Functions::extractField('name'));
    }

    public function wrapErrors($errors)
    {
        $result = new stdClass();
        $result->result = false;
        $result->errors = $errors;
        return $result;
    }

    public function serviceAuth($object)
    {
        if ($object->token != 'test_token') {
            throw new SoapFault('WT', 'Wrong token');
        } else {
            $this->clientId = $object->id;
        }
    }

    public function authorizedMethod($name, $surname)
    {
        return 'clientId [' . $this->clientId . '] name [' . $name . '] surname [' . $surname . ']';
    }

    public function methodWithoutReturn($userToken)
    {
        file_put_contents('/tmp/wsdl-creator-example-log', $userToken . PHP_EOL, FILE_APPEND);
    }

    public function methodWithoutParameters()
    {
        return 'method without parameters';
    }
}
