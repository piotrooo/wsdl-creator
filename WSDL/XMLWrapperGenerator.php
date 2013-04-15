<?php
/**
 * XMLWrapperGenerator
 *
 * @author Piotr Olaszewski
 */
namespace WSDL;

class XMLWrapperGenerator 
{
    private $_targetNamespace;
    private $_DOMDocument;
    private $_generatedXML;

    public function __construct($targetNamespace)
    {
        $this->_targetNamespace = $targetNamespace;
        $this->_DOMDocument = new \DOMDocument("1.0", "UTF-8");

        $this->_saveXML();
    }

    private function _saveXML()
    {
        $this->_generatedXML = $this->_DOMDocument->saveXML();
    }

    public function setDefinitions()
    {
        $definitionsElement = $this->_DOMDocument->createElement('definitions');

        $attribute = $this->_DOMDocument->createAttribute('targetNamespace');
        $attribute->value = $this->_targetNamespace;
        $definitionsElement->appendChild($attribute);

        $this->_DOMDocument->appendChild($definitionsElement);

        $this->_saveXML();

        return $this;
    }

    public function render()
    {
//        var_dump( $this->_generatedXML);
        echo $this->_generatedXML;
    }
}