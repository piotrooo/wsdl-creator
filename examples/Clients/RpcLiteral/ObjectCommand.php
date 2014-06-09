<?php
namespace Clients\RpcLiteral;

use Clients\InitCommand;
use SoapClient;
use stdClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ObjectCommand extends InitCommand
{
    protected function configure()
    {
        $this->setName('clients:rpc_literal_object');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->soapClient = new SoapClient('http://localhost/wsdl-creator/examples/rpc_literal/ObjectExampleSoapServer.php?wsdl', array(
            'uri' => "http://foo.bar/", 'location' => 'http://localhost/wsdl-creator/examples/rpc_literal/ObjectExampleSoapServer.php',
            'trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE
        ));

        print_r($this->soapClient->__getFunctions());

        $user = new stdClass();
        $user->name = 'john';
        $user->age = 32;
        $response = $this->soapClient->userInfo($user);
        $this->_method('userInfo', $response);

        $response = $this->soapClient->getAgentWithId('peter', 999444);
        $this->_method('getAgentWithId', $response);

        $namesInfo = new stdClass();
        $namesInfo->names = array('billy', 'clark');
        $namesInfo->id = 333;
        $response = $this->soapClient->namesForId($namesInfo);
        $this->_method('namesForId', $response);

        $response = $this->soapClient->getCompanies();
        $this->_method('getCompanies', $response);

//        $response = $this->soapClient->getListOfAgentsWithId();
//        $this->_method('getListOfAgentsWithId', '$this->soapClient->getListOfAgentsWithId()', $response);

        $payments[0] = new stdClass();
        $payments[0]->payment = array(1.21, 3.21, 100.60);
        $payments[0]->user = 'john';
        $payments[1] = new stdClass();
        $payments[1]->payment = array(120.60);
        $payments[1]->user = 'peter';
        $response = $this->soapClient->setPayment($payments);
        $this->_method('setPayment', $response);

        $response = $this->soapClient->getAgentsWithPayment();
        $this->_method('getAgentsWithPayment', $response);

        $response = $this->soapClient->getEmployeesWithAgents();
        $this->_method('getEmployeesWithAgents', $response);
    }
}