<?php
namespace Clients\RpcEncoded;

use Clients\InitCommand;
use SoapClient;
use stdClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ObjectCommand extends InitCommand
{
    protected function configure()
    {
        $this->setName('rpc_encoded:object');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->soapClient = new SoapClient('http://localhost/wsdl-creator/examples/rpc_encoded/ObjectExampleSoapServer.php?wsdl', array(
            'uri' => "http://foo.bar/", 'location' => 'http://localhost/wsdl-creator/examples/rpc_encoded/ObjectExampleSoapServer.php',
            'trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE
        ));

        $this->serviceInfo('Client Object - rpc/encoded');

        $this->renderMethodsTable();

        $user = new stdClass();
        $user->name = 'john';
        $user->age = 32;
        $response = $this->soapClient->userInfo($user);
        $this->method('userInfo', array($user), $response);

        $response = $this->soapClient->getAgentWithId('peter', 999444);
        $this->method('getAgentWithId', array('peter', 999444), $response);

        $namesInfo = new stdClass();
        $namesInfo->names = array('billy', 'clark');
        $namesInfo->id = 333;
        $response = $this->soapClient->namesForId($namesInfo);
        $this->method('namesForId', array($namesInfo), $response);

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
        $response = $this->soapClient->setPayment($payments);
        $this->method('setPayment', array($payments), $response);

        $response = $this->soapClient->getAgentsWithPayment();
        $this->method('getAgentsWithPayment', array(), $response);

        $response = $this->soapClient->getEmployeesWithAgents();
        $this->method('getEmployeesWithAgents', array(), $response);
    }
}