<?php
namespace WSDL\Builder;

class Method
{
    private $name;
    private $parameters;
    private $return;

    public function __construct($name, $parameters, $return)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->return = $return;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getReturn()
    {
        return $this->return;
    }
}
