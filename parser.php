<?php

/*
Grammar:

S -> P
P -> T R I
I -> P
I -> e
R -> '[]' N O
R -> N O
O -> '{' P '}'
O -> e
N -> 'token=name'
T -> 'token=type'
 */

class Node
{
    private $type;
    private $name;
    private $isArray;
    private $elements;

    public function __construct($type, $name, $isArray, array $elements = [])
    {
        $this->type = $type;
        $this->name = $name;
        $this->isArray = (bool)$isArray;
        $this->elements = $elements;
    }
}

class Parser
{
    private $tokens;
    private $position;

    public function __construct($tokens)
    {
        $this->tokens = $tokens;
        $this->position = 0;
    }

    public function S()
    {
        return $this->P();
    }

    private function P()
    {
        $type = $this->T();
        list($isArray, $name, $elements) = $this->R();
        $nodes = $this->I();
        $node = new Node($type, $name, $isArray, $elements);
        array_unshift($nodes, $node);
        return $nodes;
    }

    private function T()
    {
        $token = $this->shift();
        if ($token->token == Token::TYPE) {
            return $token->value;
        }
        throw new Exception('Parse error - wrong type');
    }

    private function R()
    {
        if ($this->lookahead()->token == Token::ARRAYS) {
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

    private function I()
    {
        if ($this->lookahead()->token != Token::EOF && $this->lookahead()->token != Token::CLOSE_OBJECT) {
            return $this->P();
        }
        return [];
    }

    private function N()
    {
        $token = $this->shift();
        if ($token->token == Token::NAME) {
            return $token->value;
        }
        throw new Exception('Parse error - wrong name');
    }

    private function O()
    {
        if ($this->lookahead()->token == Token::OPEN_OBJECT) {
            $this->shift();
            $node = $this->P();
            $this->shift();
            return $node;
        }
        return [];
    }

    private function lookahead()
    {
        return $this->tokens[$this->position];
    }

    private function shift()
    {
        $token = $this->lookahead();
        $this->position++;
        return $token;
    }
}

class Token
{
    const TYPE = 'type';
    const NAME = 'name';
    const ARRAYS = 'array';
    const OPEN_OBJECT = 'open_object';
    const CLOSE_OBJECT = 'close_object';
    const EOF = 'eof';
}

class Tokenizer
{
    private static $tokenMap = [
        '/\s*\w+\s*/Am' => Token::TYPE,
        '/\s*\$\w+\s*/Am' => Token::NAME,
        '/\s*\[\]\s*/Am' => Token::ARRAYS,
        '/\s*\{\s*/Am' => Token::OPEN_OBJECT,
        '/\s*\}\s*/Am' => Token::CLOSE_OBJECT,
    ];

    public function lex($string)
    {
        $tokens = [];
        $offset = 0;
        while (isset($string[$offset])) {
            foreach (self::$tokenMap as $regex => $token) {
                if (preg_match($regex, $string, $matches, null, $offset)) {
                    $tokenDetails = new stdClass();
                    $tokenDetails->token = $token;
                    $tokenDetails->value = trim($matches[0]);
                    $tokens[] = $tokenDetails;
                    $offset += strlen($matches[0]);
                    continue 2;
                }
            }
            throw new Exception(sprintf('Unexpected character: >%s< offset >%d<', $string[$offset], $offset));
        }
        $eofToken = new stdClass();
        $eofToken->token = Token::EOF;
        $eofToken->value = 'eof';
        $tokens[] = $eofToken;
        return $tokens;
    }
}

$params = '
    int $age
    int[] $ages
    object $agent {
        string $name
        int $count
    }';

$tokenizer = new Tokenizer();
$tokens = $tokenizer->lex($params);
$tree = new Parser($tokens);
echo 'Param: ' . $params . PHP_EOL;
print_r($tree->S());
echo '===' . PHP_EOL;
