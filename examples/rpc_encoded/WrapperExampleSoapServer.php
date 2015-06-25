<?php
use WSDL\WSDLCreator;
use WSDL\XML\Styles\RpcEncoded;

require_once '../../vendor/autoload.php';

$wsdl = new WSDLCreator('WrapperSoapServer', 'http://localhost/wsdl-creator/examples/rpc_encoded/WrapperExampleSoapServer.php');
$wsdl->setNamespace("http://foo.bar/")->setBindingStyle(new RpcEncoded());

if (isset($_GET['wsdl'])) {
    $wsdl->renderWSDL();
    exit;
}

$wsdl->renderWSDLService();

$server = new SoapServer('http://localhost/wsdl-creator/examples/rpc_encoded/WrapperExampleSoapServer.php?wsdl', array(
    'uri' => $wsdl->getNamespaceWithSanitizedClass(),
    'location' => $wsdl->getLocation(),
    'style' => SOAP_RPC,
    'use' => SOAP_ENCODED
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

class Employee
{
    /**
     * @type int
     */
    public $id;
    /**
     * @type string
     */
    public $department;
}

class WrapperSoapServer
{
    /**
     * @WebMethod
     * @param wrapper $user @className=User
     * @param int $id
     * @return string $nameWithAge
     */
    public function getUserString($user, $id)
    {
        return '[#' . $id . ']Your name is: ' . $user->name . ' and you have ' . $user->age . ' years old with payment ' . $user->payment;
    }

    /**
     * @WebMethod
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

    /**
     * @WebMethod
     * @return wrapper[] $employees @className=Employee
     */
    public function getEmployees()
    {
        $employees = array();
        $departments = array('IT', 'Logistics', 'Management');
        for ($i = 0; $i < 3; $i++) {
            $employee = new Employee();
            $employee->id = 2 + $i + 1;
            $employee->department = $departments[$i];
            $employees[] = $employee;
        }
        return $employees;
    }

    /**
     * @WebMethod
     * @param wrapper[] $employeesList @className=Employee
     * @return string $str
     */
    public function getEmployeesDepartments($employeesList)
    {
        $names = array();
        foreach ($employeesList as $employee) {
            $names[] = $employee->department;
        }
        return implode(', ', $names);
    }
}