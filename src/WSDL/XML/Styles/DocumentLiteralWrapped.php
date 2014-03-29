<?php
/**
 * DocumentLiteralWrapped
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

use WSDL\Parser\MethodParser;

class DocumentLiteralWrapped implements Style
{
    public function bindingStyle()
    {
        return 'document';
    }

    public function bindingUse()
    {
        return 'literal';
    }

    public function methodInput(MethodParser $method)
    {
        return array($this->_createPart($method, 'Request'));
    }

    public function methodOutput(MethodParser $method)
    {
        return $this->_createPart($method, 'Response');
    }

    private function _createPart(MethodParser $method, $type = '')
    {
        $name = 'parameters';
        $elementName = $method->getName() . $type . 'Parameters';
        $element = array(
            'name' => $name,
            'element' => $elementName
        );
        return $element;
    }

    public function typeParameters(MethodParser $method)
    {

    }

    public function typeReturning(MethodParser $method)
    {

    }
}