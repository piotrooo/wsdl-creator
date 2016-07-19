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

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;
use WSDL\Parser\Node;

/**
 * Method
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class Method
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var Parameter[]
     */
    private $parameters;
    /**
     * @var Parameter
     */
    private $return;

    /**
     * @param string $name
     * @param Parameter[] $parameters
     * @param Parameter $return
     */
    public function __construct($name, array $parameters, Parameter $return)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->return = $return;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Node[]
     */
    public function getParametersNodes()
    {
        return Arrays::map($this->parameters, Functions::extractExpression('getNode()'));
    }

    /**
     * @return Node
     */
    public function getReturnNode()
    {
        return $this->return->getNode();
    }

    /**
     * @return Parameter|null
     */
    public function parameterHeader()
    {
        return Arrays::find($this->parameters, function (Parameter $parameter) {
            return $parameter->isHeader();
        });
    }

    /**
     * @return Parameter[]|array
     */
    public function noParameterHeader()
    {
        return Arrays::filter($this->parameters, function (Parameter $parameter) {
            return !$parameter->isHeader();
        });
    }

    /**
     * @return Node[]
     */
    public function getNoParameterHeaderNodes()
    {
        return Arrays::map($this->noParameterHeader(), Functions::extractExpression('getNode()'));
    }

    /**
     * @return Parameter|null
     */
    public function returnHeader()
    {
        if ($this->return->isHeader()) {
            return $this->return;
        }
        return null;
    }
}
