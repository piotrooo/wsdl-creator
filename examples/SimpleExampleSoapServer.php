<?php
require_once '../vendor/autoload.php';

use WSDL\WSDLCreator;

$wsdl = new WSDL\WSDLCreator('SimpleSoapServer', 'http://localhost/wsdl-creator/examples/SimpleExampleSoapServer.php');
$wsdl->setNamespace("http://foo.bar/");

if (isset($_GET['wsdl'])) {
    $wsdl->renderWSDL();
    exit;
}

$wsdl->renderWSDLService();

$server = new SoapServer(null, array(
    'uri' => 'http://localhost/wsdl-creator/examples/SimpleExampleSoapServer.php'
));
$server->setClass('SimpleSoapServer');
$server->handle();

class SimpleSoapServer
{
    /**
     * @param string $name
     * @param int $age
     * @return string $nameWithAge
     */
    public function getNameWithAge($name, $age)
    {
        return 'Your name is: ' . $name . ' and you have ' . $age . ' years old';
    }

    /**
     * @param string[] $names
     * @return string $userNames
     */
    public function getNameForUsers($names)
    {
        return 'User names: ' . implode(', ', $names);
    }

    /**
     * @param int $max
     * @return string[] $count
     */
    public function countTo($max)
    {
        $array = array();
        for ($i = 0; $i < $max; $i++) {
            $array[] = 'Number: ' . ($i + 1);
        }
        return $array;
    }
}