<?php
namespace WSDL\Builder;

class WSDLBuilder
{
    private $name;
    private $targetNamespace;
    private $ns;
    private $location;
    private $style;
    private $use;
    private $methods;

    public static function instance()
    {
        return new self();
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getTargetNamespace()
    {
        return $this->targetNamespace;
    }

    public function setTargetNamespace($targetNamespace)
    {
        $this->targetNamespace = $targetNamespace;
        return $this;
    }

    public function getNs()
    {
        return $this->ns;
    }

    public function setNs($ns)
    {
        $this->ns = $ns;
        return $this;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    public function getStyle()
    {
        return $this->style;
    }

    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    public function getUse()
    {
        return $this->use;
    }

    public function setUse($use)
    {
        $this->use = $use;
        return $this;
    }

    /**
     * @return Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    public function setMethod(Method $method)
    {
        $this->methods[] = $method;
        return $this;
    }
}
