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

use InvalidArgumentException;
use WSDL\Annotation\BindingType;
use WSDL\Annotation\SoapBinding;

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
    private $style = SoapBinding::RPC;
    /**
     * @var string
     */
    private $use = SoapBinding::LITERAL;
    /**
     * @var string
     */
    private $parameterStyle = SoapBinding::BARE;
    /**
     * @var string
     */
    private $soapVersion = BindingType::SOAP_11;
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
     * @throws InvalidArgumentException
     */
    public function setName($name)
    {
        IsValid::notEmpty($name, 'Name cannot be empty');
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
     * @throws InvalidArgumentException
     */
    public function setTargetNamespace($targetNamespace)
    {
        IsValid::notEmpty($targetNamespace, 'Target namespace cannot be empty');
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
     * @throws InvalidArgumentException
     */
    public function setNs($ns)
    {
        IsValid::notEmpty($ns, 'NS cannot be empty');
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
     * @throws InvalidArgumentException
     */
    public function setLocation($location)
    {
        IsValid::notEmpty($location, 'Location cannot be empty');
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
     * @throws InvalidArgumentException
     */
    public function setStyle($style)
    {
        IsValid::style($style);
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
        IsValid::useStyle($use);
        $this->use = $use;
        return $this;
    }

    /**
     * @return string
     */
    public function getParameterStyle()
    {
        return $this->parameterStyle;
    }

    /**
     * @param string $parameterStyle
     * @return $this
     */
    public function setParameterStyle($parameterStyle)
    {
        $this->parameterStyle = $parameterStyle;
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
     * @throws InvalidArgumentException
     */
    public function setSoapVersion($soapVersion)
    {
        IsValid::soapVersion($soapVersion);
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
