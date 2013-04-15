<?php
require_once 'WSDL/WSDLCreator.php';

if (isset($_GET['wsdl'])) {
    $wsdl = new \WSDL\WSDLCreator('ExampleSoapServer');
    $wsdl->renderWSDL();
}

class ExampleSoapServer
{
    private function _toLog($message)
    {
        file_put_contents('/tmp/logs_soap.log', $message);
    }

    /**
     * @desc Method do show user name from array
     *
     * @param string $idUser
     * @param int $age
     * @return string
     */
    public function showUserName($idUser, $age)
    {
        $usersName = array('John', 'Peter');

        if (!empty($usersName[$idUser])) {
            return $usersName[$idUser];
        } else {
            return '';
        }
    }
}