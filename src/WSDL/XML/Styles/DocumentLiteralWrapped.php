<?php
/**
 * DocumentLiteralWrapped
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
namespace WSDL\XML\Styles;

use WSDL\Parser\MethodParser;
use WSDL\Types\Type;
use WSDL\Utilities\TypeHelper;

class DocumentLiteralWrapped extends Style
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
        return array($this->_createPart($method));
    }

    public function methodOutput(MethodParser $method)
    {
        return $this->_createPart($method, 'Response');
    }

    private function _createPart(MethodParser $method, $type = '')
    {
        $name = 'parameters';
        $elementName = 'ns:' . $method->getName() . $type;
        $element = array(
            'name' => $name,
            'element' => $elementName
        );
        return $element;
    }

    public function typeParameters(MethodParser $method)
    {
        $element = new TypesElement();
        $element->setName($method->getName());
        foreach ($method->parameters() as $parameter) {
            $this->_generateElements($parameter, $element);
        }
        return array($element);
    }

    public function typeReturning(MethodParser $method)
    {
        $element = new TypesElement();
        $element->setName($method->getName() . 'Response');
        $returning = $method->returning();
        $this->_generateElements($returning, $element);
        return $element;
    }

    private function _generateElements(Type $parameter, TypesElement $element)
    {
        list($type, $value) = $this->_prepareTypeAndValue($parameter);
        $element->setElementAttributes($type, $value, $parameter->getName());
        if (!TypeHelper::isSimple($parameter)) {
            $complexType = $this->_generateComplexType($parameter);
            $element->setComplex($complexType);
        }
    }
}