<?php

class Simple
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

class Arrays
{
    private $name;
    private $object;

    public function __construct($name, $object)
    {
        $this->name = $name;
        $this->object = $object;
    }

    public function getType()
    {
        return 'array';
    }

    public function getName()
    {
        return $this->name;
    }

    public function getObject()
    {
        return $this->object;
    }
}

class Object
{
    private $name;
    private $complexRepository;
    private $type;

    public function __construct($name, $complexRepository, $type = 'object')
    {
        $this->name = $name;
        $this->complexRepository = $complexRepository;
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getComplexRepository()
    {
        return $this->complexRepository;
    }
}

class ComplexRepository
{
    private $elements;

    public function add($type)
    {
        $this->elements[] = $type;
        return $this;
    }

    public function getElements()
    {
        return $this->elements;
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
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Simple('string', 'name'))
    ->add(new Simple('int', 'count'));
$parameters[] = new Object('obj1', $complexRepository);

//@param object $obj2 @string[]=$name @int=$count
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Arrays('array_of_name', new Simple('string', 'name')))
    ->add(new Simple('int', 'count'));
$parameters[] = new Object('obj2', $complexRepository);

//@param object[] $obj3 @string=$name @int=$count
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Simple('string', 'name'))
    ->add(new Simple('int', 'count'));
$parameters[] = new Arrays('array_of_obj3', new Object('obj3', $complexRepository));

//@param object[] $obj4 @string=$name @int[]=$count
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Simple('string', 'name'))
    ->add(new Arrays('array_of_count', new Simple('int', 'count')));
$parameters[] = new Arrays('array_of_obj4', new Object('obj4', $complexRepository));

//@param object $obj5 @(object $obj51 @string=name @int=age) @int=$count
$complexRepository51 = new ComplexRepository();
$complexRepository51
    ->add(new Simple('string', 'name'))
    ->add(new Simple('int', 'age'));
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Object('obj51', $complexRepository51))
    ->add(new Simple('int', 'count'));
$parameters[] = new Object('obj5', $complexRepository);

//@param object $obj6 @(object $obj61 @string[]=name @int=age) @int=$count
$complexRepository61 = new ComplexRepository();
$complexRepository61
    ->add(new Arrays('array_of_name', new Simple('string', 'name')))
    ->add(new Simple('int', 'age'));
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Object('obj61', $complexRepository61))
    ->add(new Simple('int', 'count'));
$parameters[] = new Object('obj6', $complexRepository);

//@param object $obj7 @(object[] $obj71 @string=name @int=age) @int=$count
$complexRepository71 = new ComplexRepository();
$complexRepository71
    ->add(new Simple('string', 'name'))
    ->add(new Simple('int', 'age'));
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Arrays('array_of_obj71', new Object('obj71', $complexRepository71)))
    ->add(new Simple('int', 'count'));
$parameters[] = new Object('obj7', $complexRepository);

//@param object $obj8 @(object[] $obj81 @string[]=name @int=age) @int=$count
$complexRepository81 = new ComplexRepository();
$complexRepository81
    ->add(new Arrays('array_of_obj81', new Simple('string', 'name')))
    ->add(new Simple('int', 'age'));
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Arrays('array_of_obj81', new Object('obj81', $complexRepository81)))
    ->add(new Simple('int', 'count'));
$parameters[] = new Object('obj8', $complexRepository);

//@param wrapper $wr1 @className=Person
$complexRepositoryWR1 = new ComplexRepository();
$complexRepositoryWR1
    ->add(new Simple('string', 'name'))
    ->add(new Simple('int', 'count'));
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Simple('int', 'age'))
    ->add(new Object('details', $complexRepositoryWR1));
$parameters[] = new Object('wr1', $complexRepository, 'wrapper');

//@param wrapper[] $wr2 @className=Person
$complexRepositoryWR1 = new ComplexRepository();
$complexRepositoryWR1
    ->add(new Simple('string', 'name'))
    ->add(new Simple('int', 'count'));
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Simple('int', 'age'))
    ->add(new Object('details', $complexRepositoryWR1));
$parameters[] = new Arrays('array_of_wr2', new Object('wr2', $complexRepository, 'wrapper'));

print_r($parameters);
