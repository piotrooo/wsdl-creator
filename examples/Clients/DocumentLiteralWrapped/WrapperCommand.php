<?php
namespace Clients\DocumentLiteralWrapped;

use Clients\InitCommand;
use SoapClient;
use stdClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WrapperCommand extends InitCommand
{
    protected function configure()
    {
        $this->setName('document_literal:wrapper');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->soapClient = new SoapClient('http://localhost/wsdl-creator/examples/document_literal_wrapped/WrapperExampleSoapServer.php?wsdl', array(
            'uri' => "http://foo.bar/", 'location' => 'http://localhost/wsdl-creator/examples/document_literal_wrapped/WrapperExampleSoapServer.php',
            'trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE
        ));

        $this->serviceInfo('Wrapper Object - document/literal wrapped');

        $this->renderMethodsTable();

        $user = new stdClass();
        $user->name = 'john';
        $user->age = 31;
        $user->payment = 123.40;
        $response = $this->soapClient->getUserString(array('user' => $user, 'id' => 333));
        $this->method('getUserString', array(array('user' => $user, 'id' => 333)), $response);

        $params = new stdClass();
        $params->name = 'peter';
        $params->age = 22;
        $params->payment = 32.02;
        $response = $this->soapClient->getUser($params);
        $this->method('getUser', array($params), $response);

        $response = $this->soapClient->getEmployees();
        $this->method('getEmployees', array(), $response);

        $employees[0] = new stdClass();
        $employees[0]->id = 1;
        $employees[0]->department = 'IT';
        $employees[1] = new stdClass();
        $employees[1]->id = 2;
        $employees[1]->department = 'Logistic';
        $response = $this->soapClient->getEmployeesDepartments(array('employeesList' => $employees));
        $this->method('getEmployeesDepartments', array(array('employeesList' => $employees)), $response);
    }
}