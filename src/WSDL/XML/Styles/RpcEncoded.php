<?php
/**
 * RpcEncoded
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

use WSDL\Parser\MethodParser;

class RpcEncoded extends Style
{
    public function bindingStyle()
    {
        return 'rpc';
    }

    public function bindingUse()
    {
        return 'encoded';
    }

    public function methodInput(MethodParser $method)
    {
        $partElements = array();
        foreach ($method->parameters() as $parameter) {
            $partElements[] = $this->_createElement($parameter);
        }
        return $partElements;
    }

    public function methodOutput(MethodParser $method)
    {
        $returnElement = $this->_createElement($method->returning());
        return $returnElement;
    }

    public function typeParameters(MethodParser $method)
    {
        $elements = array();
        foreach ($method->parameters() as $parameter) {
            $elements[] = $this->_generateType($parameter);
        }
        return $elements;
    }

    public function typeReturning(MethodParser $method)
    {
        return $this->_generateType($method->returning());
    }
}