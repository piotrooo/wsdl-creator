<?php
require_once 'vendor/autoload.php';

use WSDL\WSDLCreator;

if (isset($_GET['wsdl'])) {
    $wsdl = new WSDL\WSDLCreator('ExampleSoapServer');
    $wsdl->renderWSDL();
}
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
     * @desc MethodParser to sum two integers
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
     * @param string $name
     * @return array
     */
    public function arrayTest($object1, $name)
    {
        return array('obj1' => $object1, 'name' => $name);
    }
}