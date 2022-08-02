<?php
/**
 * Copyright (C) 2013-2022
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
use Ouzo\Utilities\Optional;
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
     * @param Parameter|null $return
     */
    public function __construct(string $name, array $parameters, Parameter $return = null)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->return = $return;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getHeaderParameter(): ?Parameter
    {
        return Arrays::find($this->parameters, function (Parameter $parameter) {
            return $parameter->isHeader();
        });
    }

    /**
     * @return Parameter[]
     */
    public function getNoHeaderParameters(): array
    {
        return Arrays::filter($this->parameters, function (Parameter $parameter) {
            return !$parameter->isHeader();
        });
    }

    /**
     * @return Node[]
     */
    public function getNoHeaderParametersNodes(): array
    {
        return Arrays::map($this->getNoHeaderParameters(), Functions::extractExpression('getNode()'));
    }

    public function getReturn(): ?Parameter
    {
        return $this->return;
    }

    public function getHeaderReturn(): ?Parameter
    {
        if (Optional::fromNullable($this->return)->isHeader()->or(false)) {
            return $this->return;
        }

        return null;
    }

    public function getReturnNode(): ?Node
    {
        return Optional::fromNullable($this->return)->getNode()->or(null);
    }
}
