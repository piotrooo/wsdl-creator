<?php
/**
 * Copyright (C) 2013-2022
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

use WSDL\Annotation\SoapBinding;
use WSDL\Builder\Parameter;
use WSDL\Parser\Node;

/**
 * XMLEncodedUseTest
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
class XMLEncodedUseTest extends XMLBaseUseTest
{
    public function useType()
    {
        return SoapBinding::ENCODED;
    }

    /**
     * @test
     */
    public function shouldGenerateSoapBody()
    {
        //when
        $soapBody = $this->XMLUse->generateSoapBody($this->DOMDocument, $this->targetNamespace, $this->soapVersion);

        //then
        $this->assertEquals('soap:body', $soapBody->tagName);
        $this->assertEquals('encoded', $soapBody->getAttribute('use'));
        $this->assertEquals($this->targetNamespace, $soapBody->getAttribute('namespace'));
        $this->assertEquals('http://schemas.xmlsoap.org/soap/encoding/', $soapBody->getAttribute('encodingStyle'));
    }

    /**
     * @test
     */
    public function shouldGenerateHeader()
    {
        //given
        $header = new Parameter(new Node('int', '$key', false), true);

        //when
        $soapHeaderIfNeeded = $this
            ->XMLUse
            ->generateSoapHeaderIfNeeded($this->DOMDocument, $this->targetNamespace, $this->soapVersion, $this->soapHeaderMessage, $header);

        //then
        $this->assertEquals('soap:header', $soapHeaderIfNeeded->tagName);
        $this->assertEquals('encoded', $soapHeaderIfNeeded->getAttribute('use'));
        $this->assertEquals($this->targetNamespace, $soapHeaderIfNeeded->getAttribute('namespace'));
        $this->assertEquals('key', $soapHeaderIfNeeded->getAttribute('part'));
        $this->assertEquals($this->soapHeaderMessage, $soapHeaderIfNeeded->getAttribute('message'));
    }
}
