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
namespace WSDL\Annotation;

use ReflectionClass;
use WSDL\Builder\WSDLBuilder;

/**
 * SoapBinding
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 *
 * @Annotation
 * @Target("CLASS")
 */
final class SoapBinding implements ClassAnnotation
{
    public const RPC = 'RPC';
    public const DOCUMENT = 'DOCUMENT';
    public const LITERAL = 'LITERAL';
    public const ENCODED = 'ENCODED';
    public const BARE = 'BARE';
    public const WRAPPED = 'WRAPPED';

    /**
     * @var string
     *
     * @Enum({"RPC", "DOCUMENT"})
     */
    public $style = self::RPC;
    /**
     * @var string
     *
     * @Enum({"LITERAL", "ENCODED"})
     */
    public $use = self::LITERAL;
    /**
     * @var string
     *
     * @Enum({"BARE", "WRAPPED"})
     */
    public $parameterStyle = self::BARE;

    public function build(WSDLBuilder $builder, ReflectionClass $class): void
    {
        $builder
            ->setStyle($this->style)
            ->setUse($this->use)
            ->setParameterStyle($this->parameterStyle);
    }
}
