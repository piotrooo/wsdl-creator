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
namespace WSDL\Parser;
use Ouzo\Utilities\Strings;

/**
 * ComplexTypeParser
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class ComplexTypeParser
{
    private $_type;
    private $_name;

    public function __construct($type, $name)
    {
        $this->_type = $type;
        $this->_name = $name;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getType()
    {
        return $this->_type;
    }

    /**
     * @param string $types
     * @return ComplexTypeParser[]
     */
    public static function create($types)
    {
        preg_match_all('#@(\((?:.+)\)|(?:.+?))(?: |$)#', $types, $matches);
        $typesArray = $matches[1];
        $obj = array_map(function ($type) {
            if (ComplexTypeParser::isReflectionType($type)) {
                $type = str_replace(array('(', ')'), '', $type);
            } else {
                $type = str_replace('=', ' ', $type);
            }
            $parser = new ParameterParser($type, '');
            return $parser->parse();
        }, $typesArray);
        return $obj;
    }

    public static function isReflectionType($types)
    {
        return Strings::contains($types, 'className');
    }
}
