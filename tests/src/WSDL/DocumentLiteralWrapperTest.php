<?php
use Mocks\MockClass;
use WSDL\DocumentLiteralWrapper;

class DocumentLiteralWrapperTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        parent::setUp();
        $this->handler = new DocumentLiteralWrapper(new MockClass());
        $this->unwrapped = new MockClass();
    }

    /**
     * @test
     */
    public function shouldWrapReturn()
    {
        $result = $this->handler->arrayOfMockUser();

        $this->assertTrue(isset($result->mockUsers));
        $this->assertEquals($result->mockUsers, $this->unwrapped->arrayOfMockUser());
    }

    /**
     * @test
     */
    public function shouldNotWrapReturn()
    {
        $result = $this->handler->sum(1, 1);

        $this->assertFalse(is_object($result));
        $this->assertEquals(2, $result);

        $result = $this->handler->noReturnFunction(1);

        $this->assertNull($result);

        $result = $this->handler->voidReturnFunction(1);

        $this->assertNull($result);
    }
}
