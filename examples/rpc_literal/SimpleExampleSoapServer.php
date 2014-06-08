<?php
use WSDL\WSDLCreator;

require_once '../../vendor/autoload.php';

$wsdl = new WSDLCreator('SimpleSoapServer', 'http://localhost/wsdl-creator/examples/rpc_literal/SimpleExampleSoapServer.php');
$wsdl->setNamespace("http://foo.bar/");

if (isset($_GET['wsdl'])) {
    $wsdl->renderWSDL();
    exit;
}

$wsdl->renderWSDLService();

$server = new SoapServer('http://localhost/wsdl-creator/examples/rpc_literal/SimpleExampleSoapServer.php?wsdl', array(
    'uri' => $wsdl->getNamespaceWithSanitizedClass(),
    'location' => $wsdl->getLocation(),
    'style' => SOAP_RPC,
    'use' => SOAP_LITERAL
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
        file_put_contents('/tmp/aaa', print_r($names, true));
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