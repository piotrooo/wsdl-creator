<?php
/**
 * ClassParserTest
 *
 * @author Piotr Olaszewski <piotroo89 [%] gmail dot com>
 */
use WSDL\Config;
use WSDL\Parser\ClassParser;

class ClassParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $config;

    public function setUp()
    {
        parent::setUp();
        $this->config = new Config();
    }

    /**
     * @test
     */
    public function shouldParseOnlyPublicMethods()
    {
        //given
        $this->config->setClass('\Mocks\MockClass');
        $classParser = new ClassParser($this->config);

        //when
        $classParser->parse();

        //then
        $this->assertCount(7, $classParser->getMethods());
    }

    /**
     * @test
     */
    public function shouldNotParseMagicMethods()
    {
        //given
        $this->config->setClass('\Mocks\MockClass');
        $classParser = new ClassParser($this->config);

        //when
        $classParser->parse();

        //then
        $this->assertCount(7, $classParser->getMethods());
    }

    /**
     * @test
     */
    public function shouldNotParseExcludedMethods()
    {
        //given
        $this->config->setClass('\Mocks\MockClass')
            ->setIgnoreMethods(['setContainer']);
        $classParser = new ClassParser($this->config);

        //when
        $classParser->parse();

        //then
        $this->assertCount(6, $classParser->getMethods());
    }
}
