<?php
/**
 * Copyright (C) 2013-2022 wsdl-creator contributors
 * This program is made available under the terms of the MIT License.
 */

namespace WsdlCreator\Internal;

use Illuminate\Support\Collection;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;
use WsdlCreator\Annotation\SOAPBinding;
use WsdlCreator\Annotation\WebMethod;
use WsdlCreator\Annotation\WebService;
use WsdlCreator\Internal\Model\Class_;
use WsdlCreator\Internal\Model\Method;
use WsdlCreator\Internal\Model\MethodParameter;
use WsdlCreator\Internal\Model\MethodReturn;
use WsdlCreator\Internal\Model\Service;

/**
 * @author Piotr Olaszewski
 */
class ServiceModelFactory
{
    public function create(ReflectionClass $implementorReflectionClass): Service
    {
        $class = $this->getClass($implementorReflectionClass);
        $methods = $this->getMethods($implementorReflectionClass);

        return new Service($class, $methods);
    }

    private function getClass(ReflectionClass $implementorReflectionClass): Class_
    {
        $webServiceAttributes = $implementorReflectionClass->getAttributes(WebService::class);

        $this->validateWebServiceAttributeExists($implementorReflectionClass, $webServiceAttributes);

        /** @var WebService $webServiceAttribute */
        $webServiceAttribute = $webServiceAttributes[0]->newInstance();

        $SOAPBindingAttributes = $implementorReflectionClass->getAttributes(SOAPBinding::class);
        if (empty($SOAPBindingAttributes)) {
            $SOAPBindingAttribute = new SOAPBinding();
        } else {
            /** @var SOAPBinding $SOAPBindingAttribute */
            $SOAPBindingAttribute = $SOAPBindingAttributes[0]->newInstance();
        }

        return new Class_($implementorReflectionClass, $webServiceAttribute, $SOAPBindingAttribute);
    }

    /**
     * @return Method
     */
    private function getMethods(ReflectionClass $implementorReflectionClass): array
    {
        $reflectionMethods = $this->getReflectionMethods($implementorReflectionClass);
        $methods = [];
        /** @var Collection<int, ReflectionMethod> $reflectionMethods */
        $reflectionMethods = collect($reflectionMethods)
            ->sortBy(fn(ReflectionMethod $reflectionMethod) => $reflectionMethod->getName());
        foreach ($reflectionMethods as $reflectionMethod) {
            $webMethodAttributes = $reflectionMethod->getAttributes(WebMethod::class);

            $webMethodAttribute = null;
            if (!empty($webMethodAttributes)) {
                /** @var WebMethod $webMethodAttribute */
                $webMethodAttribute = $webMethodAttributes[0]->newInstance();
            }

            $docComment = $reflectionMethod->getDocComment();
            $docBlock = null;
            if ($docComment !== false) {
                $docBlockFactory = DocBlockFactory::createInstance();
                $docBlock = $docBlockFactory->create($docComment);
            }

            $methodParameters = $this->getMethodParameters($docBlock, $reflectionMethod);
            $methodReturn = $this->getMethodReturn($docBlock, $reflectionMethod);

            $methods[] = new Method($reflectionMethod, $webMethodAttribute, $methodParameters, $methodReturn);
        }
        return $methods;
    }

    private function getMethodParameters(?DocBlock $docBlock, ReflectionMethod $reflectionMethod): array
    {
        $map = collect();
        $docBlockParameters = [];
        if (!is_null($docBlock)) {
            $docBlockParameters = $docBlock->getTagsByName('param');
        }
        if (!empty($docBlockParameters)) {

            $map = collect($docBlockParameters)
                ->mapWithKeys(fn(Param $param, int $key) => [$param->getVariableName() => $param]);
        }

        $methodParameters = [];
        $reflectionParameters = $reflectionMethod->getParameters();
        foreach ($reflectionParameters as $reflectionParameter) {
            $param = $map->get($reflectionParameter->getName());
            $methodParameters[] = new MethodParameter($reflectionParameter, $param);
        }
        return $methodParameters;
    }

    private function getMethodReturn(?DocBlock $docBlock, ReflectionMethod $reflectionMethod): MethodReturn
    {
        $docBlockReturn = null;
        if (!is_null($docBlock)) {
            $docBlockReturn = collect($docBlock->getTagsByName('return'))->first();
        }

        $reflectionUnionType = $reflectionMethod->getReturnType();
        return new MethodReturn($reflectionUnionType, $docBlockReturn);
    }

    private function validateWebServiceAttributeExists(ReflectionClass $implementorReflectionClass, array $webServiceAttributes): void
    {
        if (empty($webServiceAttributes)) {
            $classShortName = $implementorReflectionClass->getShortName();
            throw new RuntimeException("{$classShortName} has not #[WebService] attribute");
        }
    }

    private function getReflectionMethods(ReflectionClass $implementorReflectionClass): Collection
    {
        $reflectionMethods = $implementorReflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        /** @var Collection<int, ReflectionMethod> $reflectionMethods */
        $reflectionMethods = collect($reflectionMethods)->filter(function (ReflectionMethod $reflectionMethod) {
            $webMethodAttributes = $reflectionMethod->getAttributes(WebMethod::class);
            if (empty($webMethodAttributes)) {
                return true;
            }

            /** @var WebMethod $webMethod */
            $webMethod = $webMethodAttributes[0]->newInstance();
            return !$webMethod->exclude();
        });
        if ($reflectionMethods->isEmpty()) {
            $classShortName = $implementorReflectionClass->getShortName();
            throw new RuntimeException("The web service defined by class {$classShortName} does not contains any valid web methods");
        }
        return $reflectionMethods;
    }
}
