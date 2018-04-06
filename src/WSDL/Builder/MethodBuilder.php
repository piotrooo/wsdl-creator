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

/**
 * MethodBuilder
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class MethodBuilder
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var Parameter[]
     */
    private $parameters = [];
    /**
     * @var Parameter
     */
    private $return;

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
     * @param Parameter $parameter
     * @return $this
     */
    public function setParameter(Parameter $parameter)
    {
        $this->parameters[] = $parameter;
        return $this;
    }

    /**
     * @param Parameter $parameter
     * @return $this
     */
    public function setReturn(Parameter $parameter)
    {
        $this->return = $parameter;
        return $this;
    }

    /**
     * @return Method
     */
    public function build()
    {
        return new Method($this->name, $this->parameters, $this->return);
    }

    /**
     * @return MethodBuilder
     */
    public static function instance()
    {
        return new self();
    }
}
