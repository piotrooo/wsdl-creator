<?php
/**
 * RpcLiteral
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

use WSDL\Types\Type;

class RpcLiteral extends Rpc implements Style
{
    public function bindingStyle()
    {
        return 'rpc';
    }

    public function bindingUse()
    {
        return 'literal';
    }

    /**
     * @param Type[] $parameters
     * @return array
     */
    public function methodInput($parameters)
    {
        $partElements = array();
        foreach ($parameters as $parameter) {
            $partElements[] = $this->_createElement($parameter);
        }
        return $partElements;
    }

    /**
     * @param Type $returning
     * @return array
     */
    public function methodOutput($returning)
    {
        $returnElement = $this->_createElement($returning);
        return $returnElement;
    }

    /**
     * @param Type[] $parameters
     * @return array
     */
    public function typeParameters($parameters)
    {
        $elements = array();
        foreach ($parameters as $parameter) {
            $elements[] = $this->_generateType($parameter);
        }
        return $elements;
    }

    public function typeReturning($returning)
    {
        return $this->_generateType($returning);
    }
}