<?php
/**
 * Created by Slava Basko <basko.slava@gmail.com>
 * Date: 5/18/15
 */

use WSDL\Config;
use WSDL\XML\Styles\DocumentLiteralWrapped;

class ConfigTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Config
     */
    private $config;

    public function setUp()
    {
        parent::setUp();
        $this->config = new Config();
    }

    public function testProperties()
    {
        $this->config->setClass('SomeClass');
        $this->assertEquals('SomeClass', $this->config->getClass());

        $this->config->setLocation('http://example.com/schema');
        $this->assertEquals('http://example.com/schema', $this->config->getLocation());

        $this->config->setLocationSuffix('wsdl');
        $this->assertEquals('wsdl', $this->config->getLocationSuffix());

        $this->config->setNamespace('http://example.com');
        $this->assertEquals('http://example.com/', $this->config->getNamespace());

        $this->config->setBindingStyle(new DocumentLiteralWrapped());
        $this->assertInstanceOf('\WSDL\XML\Styles\DocumentLiteralWrapped', $this->config->getBindingStyle());

        $this->config->setIgnoreMethods([
            'setContainer'
        ]);
        $this->assertInternalType('array', $this->config->getIgnoreMethods());
        $this->assertEquals('setContainer', $this->config->getIgnoreMethods()[0]);

        $this->assertEquals('http://example.com/schema?wsdl', $this->config->getWsdlLocation());

        $this->assertEquals('http://example.com/someclass', $this->config->getNamespaceWithSanitizedClass());
    }

}
