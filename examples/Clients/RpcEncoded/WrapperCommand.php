<?php
namespace Clients\RpcEncoded;

use Clients\InitCommand;
use SoapClient;
use stdClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WrapperCommand extends InitCommand
{
    protected function configure()
    {
        $this->setName('rpc_encoded:wrapper');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->soapClient = new SoapClient('http://localhost/wsdl-creator/examples/rpc_encoded/WrapperExampleSoapServer.php?wsdl', array(
            'uri' => "http://foo.bar/", 'location' => 'http://localhost/wsdl-creator/examples/rpc_encoded/WrapperExampleSoapServer.php',
            'trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE
        ));

        $this->serviceInfo('Wrapper Object - rpc/encoded');

        $this->renderMethodsTable();

        $user = new stdClass();
        $user->name = 'john';
        $user->age = 31;
        $user->payment = 123.40;
        $response = $this->soapClient->getUserString($user, 333);
        $this->method('getUserString', array($user, 333), $response);

        $response = $this->soapClient->getUser('peter', 22, 32.02);
        $this->method('getUser', array('peter', 22, 32.02), $response);

        $response = $this->soapClient->getEmployees();
        $this->method('getEmployees', array(), $response);

        $employees[0] = new stdClass();
        $employees[0]->id = 1;
        $employees[0]->department = 'IT';
        $employees[1] = new stdClass();
        $employees[1]->id = 2;
        $employees[1]->department = 'Logistic';
        $response = $this->soapClient->getEmployeesDepartments($employees);
        $this->method('getEmployeesDepartments', array($employees), $response);
    }
}