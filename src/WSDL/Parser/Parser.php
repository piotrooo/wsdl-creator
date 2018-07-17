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
namespace WSDL\Parser;

use Ouzo\Utilities\Strings;
use WSDL\Lexer\Token;
use WSDL\Lexer\TokenObject;

/**
 * Parser
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 *
 * Grammar:
 *
 * S -> P
 * P -> T R I
 * I -> P
 * I -> e
 * R -> '[]' N O
 * R -> N O
 * O -> '{' P '}'
 * O -> e
 * N -> 'token_name'
 * T -> 'token_type'
 */
class Parser
{
    const OBJECT_TYPE = 'object';

    /**
     * @var TokenObject[]
     */
    private $tokens;
    /**
     * @var int
     */
    private $position;

    /**
     * @param TokenObject[] $tokens
     */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->position = 0;
    }

    /**
     * @return Node[]
     */
    public function S(): array
    {
        return $this->P();
    }

    /**
     * @return Node[]
     */
    private function P(): array
    {
        $type = $this->T();
        list($isArray, $name, $elements) = $this->R();
        $nodes = $this->I();
        $node = new Node($type, $name, $isArray, $elements);
        array_unshift($nodes, $node);

        return $nodes;
    }

    private function T(): string
    {
        $token = $this->shift();
        if ($token->getName() == Token::TYPE) {
            return $token->getValue();
        }
        throw new ParserException('Wrong type');
    }

    private function R(): array
    {
        if ($this->lookahead()->getName() == Token::ARRAYS) {
            $this->shift();
            $name = $this->N();
            $elements = $this->O();
            $isArray = true;
        } else {
            $name = $this->N();
            $elements = $this->O();
            $isArray = false;
        }

        return [$isArray, $name, $elements];
    }

    /**
     * @return Node[]
     */
    private function I(): array
    {
        if ($this->lookahead()->getName() != Token::EOF && $this->lookahead()->getName() != Token::CLOSE_OBJECT) {
            return $this->P();
        }

        return [];
    }

    private function N(): string
    {
        $token = $this->shift();
        if (in_array($token->getName(), [Token::NAME, Token::CLASS_NAME])) {
            return $token->getValue();
        }
        throw new ParserException('Wrong name');
    }

    /**
     * @return Node[]
     */
    private function O(): array
    {
        $token = $this->lookahead();
        $this->checkObjectHasOpenBracket($token);
        if ($token->getName() == Token::OPEN_OBJECT) {
            $this->shift();
            $node = $this->P();
            $this->checkObjectHasCloseBracket();

            return $node;
        }

        return [];
    }

    private function checkObjectHasOpenBracket(TokenObject $token): void
    {
        $tokenObject = $this->lookAt(($this->position - 2));
        if ($tokenObject && Strings::equalsIgnoreCase($tokenObject->getValue(), self::OBJECT_TYPE) && $token->getName() != Token::OPEN_OBJECT) {
            throw new ParserException('Missing open object');
        }
    }

    private function checkObjectHasCloseBracket(): void
    {
        $token = $this->shift();
        if ($token->getName() != Token::CLOSE_OBJECT) {
            throw new ParserException('Missing close object');
        }
    }

    private function lookahead(): TokenObject
    {
        return $this->tokens[$this->position];
    }

    private function lookAt($position): TokenObject
    {
        return $this->tokens[$position];
    }

    private function shift(): TokenObject
    {
        $token = $this->lookahead();
        $this->position++;

        return $token;
    }
}
