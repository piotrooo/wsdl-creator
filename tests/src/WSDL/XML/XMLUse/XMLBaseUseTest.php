<?php
/**
 * Copyright (C) 2013-2016
 * Piotr Olaszewski <piotroo89@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Tests\WSDL\XML\XMLUse;

use DOMDocument;
use PHPUnit_Framework_TestCase;
use WSDL\Annotation\BindingType;
use WSDL\XML\XMLSoapVersion\XMLSoapVersion;
use WSDL\XML\XMLUse\XMLUse;
use WSDL\XML\XMLUse\XMLUseFactory;

/**
 * XMLBaseUseTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
abstract class XMLBaseUseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DOMDocument
     */
    protected $DOMDocument;
    /**
     * @var XMLUse
     */
    protected $XMLUse;
    protected $soapVersion;
    protected $targetNamespace = 'http://foo.bar/rpcliteralservice';
    protected $soapHeaderMessage = 'tns:MethodRequestHeader';

    public function setUp()
    {
        parent::setUp();
        $this->DOMDocument = new DOMDocument();
        $this->XMLUse = XMLUseFactory::create($this->useType());
        $this->soapVersion = XMLSoapVersion::getTagFor(BindingType::SOAP_11);
    }

    abstract public function useType();

    /**
     * @test
     */
    public function shouldNotGenerateHeaderWhenIsNotNeeded()
    {
        //when
        $soapHeaderIfNeeded = $this
            ->XMLUse
            ->generateSoapHeaderIfNeeded($this->DOMDocument, $this->targetNamespace, $this->soapHeaderMessage, null, $this->soapVersion);

        //then
        $this->assertNull($soapHeaderIfNeeded);
    }
}
