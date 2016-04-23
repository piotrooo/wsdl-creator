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
namespace WSDL\Lexer;

use Exception;
use stdClass;

/**
 * Tokenizer
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class Tokenizer
{
    /**
     * @var array
     */
    private static $tokenMap = array(
        '/\s*\w+\s*/Am' => Token::TYPE,
        '/\s*\$\w+\s*/Am' => Token::NAME,
        '/\s*\[\]\s*/Am' => Token::ARRAYS,
        '/\s*\{\s*/Am' => Token::OPEN_OBJECT,
        '/\s*\}\s*/Am' => Token::CLOSE_OBJECT
    );

    /**
     * @param string $string
     * @return array
     * @throws Exception
     */
    public function lex($string)
    {
        $tokens = [];
        $offset = 0;
        while (isset($string[$offset])) {
            foreach (self::$tokenMap as $regex => $token) {
                if (preg_match($regex, $string, $matches, null, $offset)) {
                    $tokens[] = $this->createTokenObject($token, trim($matches[0]));
                    $offset += strlen($matches[0]);
                    continue 2;
                }
            }
            throw new Exception(sprintf('Unexpected character: >%s< offset >%d<', $string[$offset], $offset));
        }
        $tokens[] = $this->createTokenObject(Token::EOF, 'eof');
        return $tokens;
    }

    private function createTokenObject($token, $value)
    {
        $tokenDetails = new stdClass();
        $tokenDetails->token = $token;
        $tokenDetails->value = $value;
        return $tokenDetails;
    }
}
