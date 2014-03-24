<?php
/**
 * RpcLiteral
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

use WSDL\Types\Type;
use WSDL\Utilities\TypeHelper;

class RpcLiteral implements Style
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
            list($type, $value) = $this->_prepareTypeAndValue($parameter);

            $partElements[] = array(
                'name' => $parameter->getName(),
                $type => $value
            );
        }
        return $partElements;
    }

    private function _prepareTypeAndValue(Type $parameter)
    {
        $type = '';
        $value = '';
        if (TypeHelper::isSimple($parameter)) {
            $type = 'type';
            $value = TypeHelper::getXsdType($parameter->getType());
        } else if (TypeHelper::isArrayType($parameter)) {
            $type = 'type';
            $value = 'ns:' . 'ArrayOf' . ucfirst($parameter->getName());
        } else if (TypeHelper::isObjectType($parameter)) {
            $type = 'element';
            $value = 'ns:' . ucfirst($this->_getObjectName($parameter));
        }
        return array($type, $value);
    }

    private function _getObjectName(Type $parameter)
    {
        return $parameter->getType() == 'object' ? $parameter->getName() : $parameter->getType();
    }
}