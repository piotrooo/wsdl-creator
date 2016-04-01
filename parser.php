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
    private $elements;

    public function __construct($type, $name, $isArray, array $elements = [])
    {
        $this->type = $type;
        $this->name = $name;
        $this->isArray = (bool)$isArray;
        $this->elements = $elements;
    }
}

class Tree
{
    private $text;
    private $position;

    public function __construct($text)
    {
        $this->text = $text;
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
        return new Node($type, $name, $isArray, $elements);
    }

    private function T()
    {
        return $this->match('/\w+/');
    }

    private function R()
    {
        if ($this->text[$this->position] == '[' && $this->text[$this->position + 1] == ']') {
            $this->match('/\[\]/');
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

    private function N()
    {
        return $this->match('/\$\w+/');
    }

    private function O()
    {
        if (isset($this->text[$this->position]) && $this->match('/\s*\{/')) {
            $elements = [];
            $elements[] = $this->P();
//            while ($this->match('/\s*\}/')) {
//            }
            return $elements;
        }
        return [];
    }

    private function match($regexp)
    {
        if (preg_match($regexp, $this->text, $m, PREG_OFFSET_CAPTURE, $this->position)) {
            $match = $m[0][0];
            $this->position = $m[0][1] + strlen($match);
            return $match;
        }
        return null;
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
    $tree = new Tree($param);
    echo 'Param: ' . $param . PHP_EOL;
    print_r($tree->S());
    echo '===' . PHP_EOL;
}
