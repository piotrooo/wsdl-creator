<?php
/**
 * Copyright (C) 2013-2019
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
namespace WSDL\Parser;

use Ouzo\Utilities\Inflector;

/**
 * Node
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class Node
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $name;
    /**
     * @var bool
     */
    private $isArray;
    /**
     * @var Node[]
     */
    private $elements;

    /**
     * @param string $type
     * @param string $name
     * @param bool $isArray
     * @param Node[] $elements
     */
    public function __construct(string $type, string $name, bool $isArray, array $elements = [])
    {
        $this->type = $type;
        $this->name = $name;
        $this->isArray = (bool)$isArray;
        $this->elements = $elements;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameForArray(): string
    {
        return 'ArrayOf' . ucfirst($this->getSanitizedName());
    }

    public function getNameForObject(): string
    {
        return Inflector::singularize(ucfirst($this->getSanitizedName()));
    }

    public function getSanitizedName(): string
    {
        return str_replace('$', '', $this->name);
    }

    public function isArray(): bool
    {
        return $this->isArray;
    }

    public function isObject(): bool
    {
        return $this->type == Parser::OBJECT_TYPE;
    }

    /**
     * @return Node[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }
}
