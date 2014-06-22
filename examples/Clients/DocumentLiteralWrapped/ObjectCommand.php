<?php
namespace Clients\DocumentLiteralWrapped;

use Clients\InitCommand;
use SoapClient;
use stdClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ObjectCommand extends InitCommand
{
    protected function configure()
    {
        $this->setName('document_literal:object');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->soapClient = new SoapClient('http://localhost/wsdl-creator/examples/document_literal_wrapped/ObjectExampleSoapServer.php?wsdl', array(
            'uri' => "http://foo.bar/", 'location' => 'http://localhost/wsdl-creator/examples/document_literal_wrapped/ObjectExampleSoapServer.php',
            'trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE
        ));

        $this->serviceInfo('Client Object - document/literal wrapped');

        $this->renderMethodsTable();

        $user = new stdClass();
        $user->name = 'john';
        $user->age = 32;
        $response = $this->soapClient->userInfo(array('info' => $user));
        $this->method('userInfo', array(array('info' => $user)), $response);

        $params = new stdClass();
        $params->name = 'peter';
        $params->number = 999444;
        $response = $this->soapClient->getAgentWithId($params);
        $this->method('getAgentWithId', array($params), $response);

        $namesInfo = new stdClass();
        $namesInfo->names = array('billy', 'clark');
        $namesInfo->id = 333;
        $response = $this->soapClient->namesForId(array('namesInfo' => $namesInfo));
        $this->method('namesForId', array(array('namesInfo' => $namesInfo)), $response);

        $response = $this->soapClient->getCompanies();
        $this->method('getCompanies', array(), $response);

        $response = $this->soapClient->getListOfAgentsWithId();
        $this->method('getListOfAgentsWithId', array(), $response);

        $payments[0] = new stdClass();
        $payments[0]->payment = array(1.21, 3.21, 100.60);
        $payments[0]->user = 'john';
        $payments[1] = new stdClass();
        $payments[1]->payment = array(120.60);
        $payments[1]->user = 'peter';
        $response = $this->soapClient->setPayment(array('payments' => $payments));
        $this->method('setPayment', array(array('payments' => $payments)), $response);

        $response = $this->soapClient->getAgentsWithPayment();
        $this->method('getAgentsWithPayment', array(), $response);

        $response = $this->soapClient->getEmployeesWithAgents();
        $this->method('getEmployeesWithAgents', array(), $response);
    }
}