<?php
/**
 * Created by Slava Basko <basko.slava@gmail.com>
 * Date: 5/18/15
 */

namespace WSDL;


use WSDL\Utilities\Strings;
use WSDL\XML\Styles\Style;

class Config {

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $locationSuffix = 'wsdl';

    /**
     * @var string
     */
    private $namespace = 'http://example.com/';

    /**
     * @var Style
     */
    private $bindingStyle;

    /**
     * @var array
     */
    private $ignoreMethods = [];

    /**
     * @param $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param $location
     * @return $this
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @param string $locationSuffix
     */
    public function setLocationSuffix($locationSuffix)
    {
        $this->locationSuffix = $locationSuffix;
    }

    /**
     * @param $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $namespace = rtrim($namespace, '/') . '/';
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @param Style $style
     * @return $this
     */
    public function setBindingStyle(Style $style = null)
    {
        $this->bindingStyle = $style;
        return $this;
    }

    /**
     * @param array $methods
     * @return $this
     */
    public function setIgnoreMethods(array $methods)
    {
        $this->ignoreMethods = $methods;
        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @return Style
     */
    public function getBindingStyle()
    {
        return $this->bindingStyle;
    }

    /**
     * @return array
     */
    public function getIgnoreMethods()
    {
        return $this->ignoreMethods;
    }

    /**
     * @return string
     */
    public function getWsdlLocation()
    {
        return $this->location . '?' . $this->locationSuffix;
    }

    /**
     * @return string
     */
    public function getLocationSuffix()
    {
        return $this->locationSuffix;
    }

    public function getNamespaceWithSanitizedClass()
    {
        return Strings::sanitizedNamespaceWithClass($this->namespace, $this->class);
    }

}