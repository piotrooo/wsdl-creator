<?php
/**
 * WrapperParserTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
use WSDL\Parser\WrapperParser;

class WrapperParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldParseWrapperClass()
    {
        //given
        $parser = new WrapperParser('\Mocks\MockUserWrapper');

        //when
        $parser->parse();

        //then
        $complex = $parser->getComplexTypes();
        $this->assertCount(3, $complex);
    }
}
