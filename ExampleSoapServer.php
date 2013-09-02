<?php
require_once 'vendor/autoload.php';

use WSDL\WSDLCreator;

if (isset($_GET['wsdl'])) {
    $wsdl = new WSDL\WSDLCreator('ExampleSoapServer', 'http://localhost/wsdl-creator/ExampleSoapServer.php');
    $wsdl->renderWSDL();
    exit;
}

$server = new SoapServer(NULL, array(
    'uri' => 'http://localhost/wsdl-creator/ExampleSoapServer.php'
));
$server->setClass('ExampleSoapServer');
$server->handle();

class ExampleSoapServer
{
    /**
     * @desc MethodParser to logging
     * @param string $message
     */
    private function _toLog($message)
    {
        file_put_contents('/tmp/logs_soap.log', $message);
    }

    /**
     * @desc to sum two integers
     * @param int $a
     * @param int $b
     * @return int
     */
    public function sum($a, $b)
    {
        return $a + $b;
    }

    /**
     * @param object $object1 @string=name @int=id
     * @return anyType
     */
    public function arrayTest($object1)
    {
        return $object1;
    }
}