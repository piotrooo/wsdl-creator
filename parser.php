<?php

/*
Grammar:

S -> P
P -> T R
R -> '[]' N O
R -> N O
O -> '{' P '}'
O -> e
N -> $\w+
T -> \w+
 */

class Node
{
    private $type;
    private $name;
    private $isArray;

    public function __construct($type, $name, $isArray)
    {
        $this->type = $type;
        $this->name = $name;
        $this->isArray = (bool)$isArray;
    }
}

class Tree
{
    private $text;
    private $position;

    public function __construct()
    {
        $this->position = 0;
    }

    function S($text)
    {
        $this->text = $text;
        $p = $this->P();
        return $p;
    }

    function P()
    {
        $type = $this->T();
        list($isArray, $name) = $this->R();
        $node = new Node($type, $name, $isArray);
        var_dump($node);
        return null;
    }

    function T()
    {
        return $this->match('/\w+/');
    }

    function R()
    {
        if ($this->text[$this->position] == '[' && $this->text[$this->position + 1] == ']') {
            $this->match('/\[\]/');
            $name = $this->N();
            $this->O();
            $isArray = true;
        } else {
            $name = $this->N();
            $this->O();
            $isArray = false;
        }
        return [$isArray, $name];
    }

    function N()
    {
        return $this->match('/\$\w+/');
    }

    function O()
    {
        if (isset($this->text[$this->position]) && $this->match('/\s*\{/')) {
            $this->P();
//            $this->match('/\s*\}/');
        }
        return '';
    }

    function match($regexp)
    {
        preg_match($regexp, $this->text, $m, 0, $this->position);
        $match = isset($m[0]) ? $m[0] : null;
        $this->position = $this->position + strlen($match);
        return $match;
    }
}

$params = [
    'int $age',
    'int[] $ages',
    'object $agent {
        string $name
        int $count
    }',
];

foreach ($params as $param) {
    $tree = new Tree();
    echo 'Param: ' . $param . PHP_EOL;
    $tree->S($param);
    echo '===' . PHP_EOL;
}
