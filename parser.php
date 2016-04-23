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
    /**
     * @var TokenObject[]
     */
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
        if ($token->getName() == Token::TYPE) {
            return $token->getValue();
        }
        throw new Exception('Parse error - wrong type');
    }

    private function R()
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

    private function I()
    {
        if ($this->lookahead()->getName() != Token::EOF && $this->lookahead()->getName() != Token::CLOSE_OBJECT) {
            return $this->P();
        }
        return [];
    }

    private function N()
    {
        $token = $this->shift();
        if ($token->getName() == Token::NAME || $token->getName() == Token::CLASS_NAME) {
            return $token->getValue();
        }
        throw new Exception('Parse error - wrong name');
    }

    private function O()
    {
        if ($this->lookahead()->getName() == Token::OPEN_OBJECT) {
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
    const CLASS_NAME = 'class_name';
    const OPEN_OBJECT = 'open_object';
    const CLOSE_OBJECT = 'close_object';
    const EOF = 'eof';
}

class TokenObject
{
    private $name;
    private $value;

    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function create($name, $value)
    {
        return new self($name, $value);
    }
}

class Tokenizer
{
    private static $tokenMap = [
        '/\s*\w+\s*/Am' => Token::TYPE,
        '/\s*\$\w+\s*/Am' => Token::NAME,
        '/\s*\[\]\s*/Am' => Token::ARRAYS,
        '/\s*\\\.+\s*/Am' => Token::CLASS_NAME,
        '/\s*\{\s*/Am' => Token::OPEN_OBJECT,
        '/\s*\}\s*/Am' => Token::CLOSE_OBJECT
    ];

    public function lex($string)
    {
        $tokens = [];
        $offset = 0;
        while (isset($string[$offset])) {
            foreach (self::$tokenMap as $regex => $token) {
                if (preg_match($regex, $string, $matches, null, $offset)) {
                    $tokens[] = TokenObject::create($token, trim($matches[0]));
                    $offset += strlen($matches[0]);
                    continue 2;
                }
            }
            throw new Exception(sprintf('Unexpected character: >%s< offset >%d<', $string[$offset], $offset));
        }
        $tokens[] = TokenObject::create(Token::EOF, 'eof');
        return $tokens;
    }
}

$params = 'object $wrap {
        className \Foo\Bar
    }';

$tokenizer = new Tokenizer();
$tokens = $tokenizer->lex($params);
print_r($tokens);
$tree = new Parser($tokens);
echo 'Param: ' . $params . PHP_EOL;
print_r($tree->S());
echo '===' . PHP_EOL;
