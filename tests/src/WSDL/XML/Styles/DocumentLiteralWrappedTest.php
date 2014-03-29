<?php
/**
 * DocumentLiteralWrappedTest
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
use WSDL\XML\Styles\DocumentLiteralWrapped;

class DocumentLiteralWrappedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DocumentLiteralWrapped
     */
    private $_documentLiteralWrapped;

    protected function setUp()
    {
        parent::setUp();
        $this->_documentLiteralWrapped = new DocumentLiteralWrapped();
    }

    /**
     * @test
     */
    public function shouldReturnCorrectBindingStyle()
    {
        //when
        $style = $this->_documentLiteralWrapped->bindingStyle();

        //then
        $this->assertEquals('document', $style);
    }

    /**
     * @test
     */
    public function shouldReturnCorrectBindingUse()
    {
        //when
        $style = $this->_documentLiteralWrapped->bindingUse();

        //then
        $this->assertEquals('literal', $style);
    }
}