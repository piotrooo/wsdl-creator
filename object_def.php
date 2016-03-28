<?php

interface Type
{
    public function getType();

    public function getName();

    public function getComplexRepository();
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

    public function getComplexRepository()
    {
        throw new Exception("This element doesn't need complex types");
    }
}

class Arrays implements Type
{
    private $name;
    private $complexRepository;

    public function __construct($name, ComplexRepository $complexRepository)
    {
        $this->name = $name;
        $this->complexRepository = $complexRepository;
    }

    public function getType()
    {
        return 'array';
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

class Object implements Type
{
    private $type;
    private $name;
    private $complexRepository;

    public function __construct($name, ComplexRepository $complexRepository, $type = 'object')
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

    public function add(Type $type)
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
$complexRepositoryArray = new ComplexRepository();
$complexRepositoryArray->add(new Simple('int', 'param_simple_int'));
$parameters[] = new Arrays('array_of_param_simple_int', $complexRepositoryArray);

//@param object $obj1 @string=$name @int=$count
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Simple('string', 'name'))
    ->add(new Simple('int', 'count'));
$parameters[] = new Object('obj1', $complexRepository);

//@param object $obj2 @string[]=$name @int=$count
$complexRepositoryArray = new ComplexRepository();
$complexRepositoryArray->add(new Simple('string', 'name'));
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Arrays('array_of_name', $complexRepositoryArray))
    ->add(new Simple('int', 'count'));
$parameters[] = new Object('obj2', $complexRepository);

//@param object[] $obj3 @string=$name @int=$count
$complexRepositoryArray = new ComplexRepository();
$complexRepositoryArray->add(new Object('obj3', $complexRepository));
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Simple('string', 'name'))
    ->add(new Simple('int', 'count'));
$parameters[] = new Arrays('array_of_obj3', $complexRepositoryArray);

//@param object[] $obj4 @string=$name @int[]=$count
$complexRepositoryArray1 = new ComplexRepository();
$complexRepositoryArray1->add(new Simple('int', 'count'));
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Simple('string', 'name'))
    ->add(new Arrays('array_of_count', $complexRepositoryArray1));
$complexRepositoryArray2 = new ComplexRepository();
$complexRepositoryArray2->add(new Object('obj4', $complexRepository));
$parameters[] = new Arrays('array_of_obj4', $complexRepositoryArray2);

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
$complexRepositoryArray = new ComplexRepository();
$complexRepositoryArray->add(new Simple('string', 'name'));
$complexRepository61 = new ComplexRepository();
$complexRepository61
    ->add(new Arrays('array_of_name', $complexRepositoryArray))
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
$complexRepositoryArray = new ComplexRepository();
$complexRepositoryArray->add(new Object('obj71', $complexRepository71));
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Arrays('array_of_obj71', $complexRepositoryArray))
    ->add(new Simple('int', 'count'));
$parameters[] = new Object('obj7', $complexRepository);

//@param object $obj8 @(object[] $obj81 @string[]=name @int=age) @int=$count
$complexRepositoryArray1 = new ComplexRepository();
$complexRepositoryArray1->add(new Simple('string', 'name'));
$complexRepository81 = new ComplexRepository();
$complexRepository81
    ->add(new Arrays('array_of_obj81', $complexRepositoryArray))
    ->add(new Simple('int', 'age'));
$complexRepositoryArray2 = new ComplexRepository();
$complexRepositoryArray2->add(new Object('obj81', $complexRepository81));
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Arrays('array_of_obj81', $complexRepositoryArray2))
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
$complexRepositoryArray = new ComplexRepository();
$complexRepositoryArray->add(new Object('wr2', $complexRepository, 'wrapper'));
$complexRepository = new ComplexRepository();
$complexRepository
    ->add(new Simple('int', 'age'))
    ->add(new Object('details', $complexRepositoryWR1));
$parameters[] = new Arrays('array_of_wr2', $complexRepositoryArray);

print_r($parameters);
