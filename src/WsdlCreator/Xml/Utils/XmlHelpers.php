<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Xml\Utils;

use ReflectionClass;

/**
 * @author Piotr Olaszewski
 */
class XmlHelpers
{
    private function __construct()
    {
    }

    public static function mapPhpTypeToWsdl(string $type): string
    {
        return match ($type) {
            'int' => 'int',
            'float' => 'float',
            'bool' => 'boolean',
            default => 'string',
        };
    }

    public static function findFqdnClass(ReflectionClass $reflectionClass, string $classShortName): string
    {
        $fileName = $reflectionClass->getFileName();
        $content = file_get_contents($fileName);

        preg_match_all('/use (.*?);/', $content, $matches);
        foreach ($matches[1] as $match) {
            if (str_ends_with($match, $classShortName)) {
                return $match;
            }
        }

        $namespaceName = $reflectionClass->getNamespaceName();
        return "{$namespaceName}{$classShortName}";
    }

    public static function classType(string $fqdnClass): string
    {
        $reflectionClass = new ReflectionClass($fqdnClass);
        $shortName = strtolower($reflectionClass->getShortName());
        return "tns:{$shortName}";
    }
}
