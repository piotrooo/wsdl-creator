<?php
namespace WSDL\Builder;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;

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

    public function getParametersNodes()
    {
        return Arrays::map($this->parameters, Functions::extractExpression('getNode()'));
    }

    public function getReturnNode()
    {
        return $this->return->getNode();
    }

    public function parameterHeader()
    {
        return Arrays::find($this->parameters, function (Parameter $parameter) {
            return $parameter->isHeader();
        });
    }

    public function returnHeader()
    {
        if ($this->return->isHeader()) {
            return $this->return;
        }
        return null;
    }
}
