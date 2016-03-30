<?php

interface Type
{
    public function getType();

    public function getName();
}

class Simple implements Type
{
    private $type;
    private $name;

    public function __construct($type, $name)
    {
        $this->type = $type;
        $this->name = $name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }
}

class Arrays implements Type
{
    private $name;
    private $element;

    public function __construct($name, Type $element)
    {
        $this->name = $name;
        $this->element = $element;
    }

    public function getType()
    {
        return 'array';
    }

    public function getName()
    {
        return $this->name;
    }

    public function getElement()
    {
        return $this->element;
    }
}

class Object implements Type
{
    private $name;
    private $elements = [];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getType()
    {
        return 'object';
    }

    public function getName()
    {
        return $this->name;
    }

    public function addElement(Type $type)
    {
        $this->elements[] = $type;
        return $this;
    }
}

class Person
{
    /**
     * @type int
     */
    public $age;

    /**
     * @type object @string=$name @int=count
     */
    public $details;
}

$parameters = [];
//@param int $param_simple_int
$parameters[] = new Simple('int', 'param_simple_int');

//@param int[] $param_simple_int
$parameters[] = new Arrays('array_of_param_simple_int', new Simple('int', 'param_simple_int'));

//@param object $obj1 @string=$name @int=$count
$obj = new Object('obj1');
$obj->addElement(new Simple('string', 'name'))
    ->addElement(new Simple('int', 'count'));
$parameters[] = $obj;

//@param object $obj2 @string[]=$name @int=$count
$obj = new Object('obj2');
$obj->addElement(new Arrays('array_of_name', new Simple('string', 'name')))
    ->addElement(new Simple('int', 'count'));
$parameters[] = $obj;

//@param object[] $obj3 @string=$name @int=$count
$obj = new Object('obj3');
$obj->addElement(new Simple('string', 'name'))
    ->addElement(new Simple('int', 'count'));
$parameters[] = new Arrays('array_of_obj3', $obj);

//@param object[] $obj4 @string=$name @int[]=$count
$obj = new Object('obj4');
$obj->addElement(new Simple('string', 'name'))
    ->addElement(new Arrays('array_of_count', new Simple('int', 'count')));
$parameters[] = new Arrays('array_of_obj4', $obj);

//@param object $obj5 @(object $obj51 @string=name @int=age) @int=$count
$obj51 = new Object('obj51');
$obj51
    ->addElement(new Simple('string', 'name'))
    ->addElement(new Simple('int', 'age'));
$obj = new Object('obj5');
$obj->addElement($obj51)
    ->addElement(new Simple('int', 'count'));
$parameters[] = $obj;

//@param object $obj6 @(object $obj61 @string[]=name @int=age) @int=$count
$obj61 = new Object('obj61');
$obj61
    ->addElement(new Arrays('array_of_name', new Simple('string', 'name')))
    ->addElement(new Simple('int', 'age'));
$obj = new Object('obj6');
$obj->addElement($obj61)
    ->addElement(new Simple('int', 'count'));
$parameters[] = $obj;

//@param object $obj7 @(object[] $obj71 @string=name @int=age) @int=$count
$obj71 = new Object('obj71');
$obj71
    ->addElement(new Simple('string', 'name'))
    ->addElement(new Simple('int', 'age'));
$obj = new Object('obj7');
$obj->addElement(new Arrays('array_of_obj71', $obj71))
    ->addElement(new Simple('int', 'count'));
$parameters[] = $obj;

//@param object $obj8 @(object[] $obj81 @string[]=name @int=age) @int=$count
$obj81 = new Object('obj81');
$obj81
    ->addElement(new Arrays('array_of_name', new Simple('string', 'name')))
    ->addElement(new Simple('int', 'age'));
$obj = new Object('obj8');
$obj->addElement(new Arrays('array_of_obj81', $obj81))
    ->addElement(new Simple('int', 'count'));
$parameters[] = $obj;

//@param wrapper $wr1 @className=Person
$wr11 = new Object('details');
$wr11
    ->addElement(new Simple('string', 'name'))
    ->addElement(new Simple('int', 'count'));
$wr1 = new Object('wr1');
$wr1->addElement(new Simple('int', 'age'))
    ->addElement($wr11);
$parameters[] = $wr1;

//@param wrapper[] $wr2 @className=Person
$wr22 = new Object('details');
$wr22
    ->addElement(new Simple('string', 'name'))
    ->addElement(new Simple('int', 'count'));
$wr2 = new Object('wr2');
$wr2->addElement(new Simple('int', 'age'))
    ->addElement($wr22);
$parameters[] = new Arrays('array_of_wr2', $wr2);

print_r($parameters);
