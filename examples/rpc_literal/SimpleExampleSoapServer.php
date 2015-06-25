<?php
use WSDL\WSDLCreator;

require_once '../../vendor/autoload.php';

ini_set("soap.wsdl_cache_enabled", 0);

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
     * @WebMethod
     * @param string $name
     * @param int $age
     * @return string $nameWithAge
     */
    public function getNameWithAge($name, $age)
    {
        return 'Your name is: ' . $name . ' and you have ' . $age . ' years old';
    }

    /**
     * @WebMethod
     * @param string[] $names
     * @return string $userNames
     */
    public function getNameForUsers($names)
    {
        return 'User names: ' . implode(', ', $names);
    }

    /**
     * @WebMethod
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