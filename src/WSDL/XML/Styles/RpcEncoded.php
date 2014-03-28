<?php
/**
 * RpcEncoded
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

class RpcEncoded implements Style
{
    public function bindingStyle()
    {
        return 'rpc';
    }

    public function bindingUse()
    {
        return 'encoded';
    }

    public function methodInput($parameters)
    {
    }

    public function methodOutput($returning)
    {
    }

    public function typeParameters($parameters)
    {
    }

    public function typeReturning($returning)
    {
    }
}