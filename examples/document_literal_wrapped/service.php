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
use WSDL\Annotation\BindingType;
use WSDL\Annotation\SoapBinding;
use WSDL\Builder\WSDLBuilder;
use WSDL\WSDL;

require_once '../../vendor/autoload.php';
require_once '../ExampleService.php';
require_once '../MethodsProvider.php';

ini_set("soap.wsdl_cache_enabled", 0);

$builder = WSDLBuilder::instance()
    ->setName('DocumentLiteralWrappedService')
    ->setTargetNamespace('http://foo.bar/documentliteralwrappedservice')
    ->setNs('http://foo.bar/documentliteralwrappedservice/types')
    ->setLocation('http://localhost:7777/wsdl-creator/examples/document_literal_wrapped/service.php')
    ->setStyle(SoapBinding::DOCUMENT)
    ->setUse(SoapBinding::LITERAL)
    ->setSoapVersion(BindingType::SOAP_11)
    ->setMethods(MethodsProvider::get());

$wsdl = WSDL::fromBuilder($builder);

if (isset($_GET['wsdl'])) {
    header("Content-Type: text/xml");
    echo $wsdl->create();
    exit;
}
$server = new SoapServer('http://localhost:7777/wsdl-creator/examples/document_literal_wrapped/service.php?wsdl', [
    'uri' => $builder->getTargetNamespace(),
    'location' => $builder->getLocation(),
    'style' => SOAP_DOCUMENT,
    'use' => SOAP_LITERAL
]);
$server->setClass('ExampleService');
$server->handle();
