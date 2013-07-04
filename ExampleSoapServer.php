<?php
require_once 'WSDL/WSDLCreator.php';

use WSDL\WSDLCreator;

if (isset($_GET['wsdl'])) {
    $wsdl = new WSDLCreator('ExampleSoapServer');
    $wsdl->renderWSDL();
}
class ExampleSoapServer
{
    /**
     * @desc Method to logging
     * @param string $message
     */
    private function _toLog($message)
    {
        file_put_contents('/tmp/logs_soap.log', $message);
    }

    /**
     * @desc Method to sum two integers
     * @param int $a
     * @param int $b
     * @return int
     */
    public function sum($a, $b)
    {
        return $a + $b;
    }

    /**
     * @param array $arr1 @string=name @int=id
     * @param array $arr2
     * @param string $name
     * @return array
     */
    public function arrayTest(array $arr1, array $arr2, $name)
    {
        return array('name' => $name);
    }
}