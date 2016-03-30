<?php
function parse_tree($string)
{
    $rules = [
        'type' => '/@param (.+?[^\[\]]) /',
        'type_array' => '/@param (.+?)\[\] /',
        'name' => '/\$(.+)/',
//        'object_attr_type' => '/(.+)/',

    ];

    $tree = [];
    $offset = 0;
    while (isset($string[$offset])) {
        foreach ($rules as $token => $rule) {
            if (preg_match($rule, $string, $matches, 0, $offset)) {
//                var_dump($rule, $matches);
                $value = $matches[1];
                if ($token == 'type_array') {
                    $token = 'type';
                    $tree['param']['element']['param'] = ['type' => $value];
                    $value = 'array';
                }
                $tree['param'][$token] = $value;
                $offset += strlen($matches[0]);
                continue 2;
            }
        }
    }
    print_r($tree);
}

$strings = [
    '@param int $age',
    '@param int[] $ages',
//    '@param object $obj1 @string=$name @int=$count',
];

foreach ($strings as $string) {
    parse_tree($string);
}

/*
 *  param:
 *      type: int
 *      name: age
 *  param:
 *      type: array
 *      name: ages
 *      element: 
 *          param:
 *              type: int
 *              name: ages
 */
