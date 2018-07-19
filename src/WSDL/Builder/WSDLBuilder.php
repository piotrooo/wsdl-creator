<?php
/**
 * Copyright (C) 2013-2018
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
     * @var string
     */
    private $portName;

    public static function instance(): WSDLBuilder
    {
        return new self();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): WSDLBuilder
    {
        IsValid::notEmpty($name, 'Name cannot be empty');
        $this->name = $name;

        return $this;
    }

    public function getTargetNamespace(): string
    {
        return $this->targetNamespace;
    }

    public function setTargetNamespace(string $targetNamespace): WSDLBuilder
    {
        IsValid::notEmpty($targetNamespace, 'Target namespace cannot be empty');
        $this->targetNamespace = $targetNamespace;

        return $this;
    }

    public function getNs(): string
    {
        return $this->ns;
    }

    public function setNs(string $ns): WSDLBuilder
    {
        IsValid::notEmpty($ns, 'NS cannot be empty');
        $this->ns = $ns;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): WSDLBuilder
    {
        IsValid::notEmpty($location, 'Location cannot be empty');
        $this->location = $location;

        return $this;
    }

    public function getStyle(): string
    {
        return $this->style;
    }

    public function setStyle(string $style): WSDLBuilder
    {
        IsValid::style($style);
        $this->style = $style;

        return $this;
    }

    public function getUse(): string
    {
        return $this->use;
    }

    public function setUse(string $use): WSDLBuilder
    {
        IsValid::useStyle($use);
        $this->use = $use;

        return $this;
    }

    public function getParameterStyle(): string
    {
        return $this->parameterStyle;
    }

    public function setParameterStyle(string $parameterStyle): WSDLBuilder
    {
        IsValid::parameterStyle($parameterStyle, $this->style);
        $this->parameterStyle = $parameterStyle;

        return $this;
    }

    public function getSoapVersion(): string
    {
        return $this->soapVersion;
    }

    public function setSoapVersion(string $soapVersion): WSDLBuilder
    {
        IsValid::soapVersion($soapVersion);
        $this->soapVersion = $soapVersion;

        return $this;
    }

    /**
     * @return Method[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    public function setMethod(Method $method): WSDLBuilder
    {
        $this->methods[] = $method;

        return $this;
    }

    /**
     * @param Method[] $methods
     * @return $this
     */
    public function setMethods(array $methods): WSDLBuilder
    {
        foreach ($methods as $method) {
            $this->setMethod($method);
        }

        return $this;
    }

    public function getPortName(): string
    {
        return $this->portName;
    }

    public function setPortName(string $portName): WSDLBuilder
    {
        $this->portName = $portName;

        return $this;
    }
}
