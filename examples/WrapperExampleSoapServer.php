<?php
require_once '../vendor/autoload.php';

use WSDL\WSDLCreator;

if (isset($_GET['wsdl'])) {
    $wsdl = new WSDL\WSDLCreator('WrapperSoapServer', 'http://localhost/wsdl-creator/examples/WrapperExampleSoapServer.php');
    $wsdl->setNamespace("http://foo.bar/");
    $wsdl->renderWSDL();
    exit;
}

$server = new SoapServer(null, array(
    'uri' => 'http://localhost/wsdl-creator/examples/WrapperExampleSoapServer.php'
));
$server->setClass('WrapperSoapServer');
$server->handle();

class User
{
    /**
     * @type string
     */
    public $name;
    /**
     * @type int
     */
    public $age;
    /**
     * @type double
     */
    public $payment;
}

class WrapperSoapServer
{
    /**
     * @param wrapper $user @className=User
     * @param int $id
     * @return string $nameWithAge
     */
    public function getUserString($user, $id)
    {
        return '[#' . $id . ']Your name is: ' . $user->name . ' and you have ' . $user->age . ' years old with payment ' . $user->payment;
    }

    /**
     * @param string $name
     * @param string $age
     * @param string $payment
     * @return wrapper $userReturn @className=User
     */
    public function getUser($name, $age, $payment)
    {
        $user = new User();
        $user->name = $name;
        $user->age = $age;
        $user->payment = $payment;
        return $user;
    }
}