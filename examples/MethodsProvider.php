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
use WSDL\Builder\Method;
use WSDL\Builder\Parameter;
use WSDL\Lexer\Tokenizer;

/**
 * MethodsProvider
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class MethodsProvider
{
    /**
     * @return Method[]
     * @throws Exception
     */
    public static function get()
    {
        $tokenizer = new Tokenizer();

        $parameters1 = [Parameter::fromTokens($tokenizer->lex('string $userName'))];
        $return1 = Parameter::fromTokens($tokenizer->lex('string $uppercasedUserName'));

        $parameters2 = [
            Parameter::fromTokens($tokenizer->lex('int[] $numbers')),
            Parameter::fromTokens($tokenizer->lex('string $prefix'))
        ];
        $return2 = Parameter::fromTokens($tokenizer->lex('string[] $numbersWithPrefix'));

        $parameters3 = [Parameter::fromTokens($tokenizer->lex('object $user { string $name int $age }'))];
        $return3 = Parameter::fromTokens($tokenizer->lex('object $userContext { int $id object $userInfo { string $name int $age } }'));

        $parameters4 = [Parameter::fromTokens($tokenizer->lex('object[] $companies { string $name int $postcode }'))];
        $return4 = Parameter::fromTokens($tokenizer->lex('string[] $companiesNames'));

        $parameters5 = [Parameter::fromTokens($tokenizer->lex('string[] $errors'))];
        $return5 = Parameter::fromTokens($tokenizer->lex('object $result { boolean $result string[] $errors }'));

        $parameters6 = [
            Parameter::fromTokens($tokenizer->lex('object $serviceAuth { string $token int $id }'), true),
            Parameter::fromTokens($tokenizer->lex('string $name')),
            Parameter::fromTokens($tokenizer->lex('string $surname'))
        ];
        $return6 = Parameter::fromTokens($tokenizer->lex('string $nameWithSurname'));

        $parameters7 = [Parameter::fromTokens($tokenizer->lex('string $userToken'))];

        $return8 = Parameter::fromTokens($tokenizer->lex('string $responseForMethodWithoutParameters'));

        return [
            new Method('uppercaseUserName', $parameters1, $return1),
            new Method('appendPrefixToNumbers', $parameters2, $return2),
            new Method('getUserContext', $parameters3, $return3),
            new Method('extractCompaniesNames', $parameters4, $return4),
            new Method('wrapErrors', $parameters5, $return5),
            new Method('authorizedMethod', $parameters6, $return6),
            new Method('methodWithoutReturn', $parameters7, null),
            new Method('methodWithoutParameters', [], $return8)
        ];
    }
}
