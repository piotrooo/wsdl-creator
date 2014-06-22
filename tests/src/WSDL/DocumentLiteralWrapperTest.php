<?php
use Mocks\MockClass;
use WSDL\DocumentLiteralWrapper;

class DocumentLiteralWrapperTest extends PHPUnit_Framework_TestCase
{
    private $_handler;
    private $_unwrapped;

    public function setUp()
    {
        parent::setUp();
        $this->_handler = new DocumentLiteralWrapper(new MockClass());
        $this->_unwrapped = new MockClass();
    }

    /**
     * @test
     */
    public function shouldWrapReturn()
    {
        //when
        $result = $this->_handler->arrayOfMockUser();

        //then
        $this->assertTrue(isset($result->mockUsers));
        $this->assertEquals($result->mockUsers, $this->_unwrapped->arrayOfMockUser());
    }

    /**
     * @test
     */
    public function shouldNotWrapReturn()
    {
        //when 1
        $params = new stdClass();
        $params->a = 1;
        $params->b = 1;
        $result = $this->_handler->sum($params);

        //then 1
        $this->assertFalse(is_object($result));
        $this->assertEquals(2, $result);

        //when 2
        $params = new stdClass();
        $params->a = 1;
        $result = $this->_handler->noReturnFunction($params);

        //then 2
        $this->assertNull($result);

        //when 3
        $params = new stdClass();
        $params->a = 1;
        $result = $this->_handler->voidReturnFunction($params);

        //then 3
        $this->assertNull($result);
    }
}
