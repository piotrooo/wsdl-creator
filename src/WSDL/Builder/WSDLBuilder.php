<?php
/**
 * Copyright (C) 2013-2016
 * Piotr Olaszewski <piotroo89@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace WSDL\Builder;

/**
 * WSDLBuilder
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class WSDLBuilder
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $targetNamespace;
    /**
     * @var string
     */
    private $ns;
    /**
     * @var string
     */
    private $location;
    /**
     * @var string
     */
    private $style;
    /**
     * @var string
     */
    private $use;
    /**
     * @var string
     */
    private $soapVersion;
    /**
     * @var Method[]
     */
    private $methods;

    /**
     * @return WSDLBuilder
     */
    public static function instance()
    {
        return new self();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getTargetNamespace()
    {
        return $this->targetNamespace;
    }

    /**
     * @param string $targetNamespace
     * @return $this
     */
    public function setTargetNamespace($targetNamespace)
    {
        $this->targetNamespace = $targetNamespace;
        return $this;
    }

    /**
     * @return string
     */
    public function getNs()
    {
        return $this->ns;
    }

    /**
     * @param string $ns
     * @return $this
     */
    public function setNs($ns)
    {
        $this->ns = $ns;
        return $this;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $location
     * @return $this
     */
    public function setLocation($location)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param string $style
     * @return $this
     */
    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    /**
     * @return string
     */
    public function getUse()
    {
        return $this->use;
    }

    /**
     * @param string $use
     * @return $this
     */
    public function setUse($use)
    {
        $this->use = $use;
        return $this;
    }

    /**
     * @return string
     */
    public function getSoapVersion()
    {
        return $this->soapVersion;
    }

    /**
     * @param string $soapVersion
     * @return $this
     */
    public function setSoapVersion($soapVersion)
    {
        $this->soapVersion = $soapVersion;
        return $this;
    }

    /**
     * @return Method[]
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param Method $method
     * @return $this
     */
    public function setMethod(Method $method)
    {
        $this->methods[] = $method;
        return $this;
    }
}
