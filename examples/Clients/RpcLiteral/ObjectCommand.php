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
        $this->_method('userInfo', '$this->soapClient->userInfo($user)', $response);

        $response = $this->soapClient->getAgentWithId('peter', 999444);
        $this->_method('getAgentWithId', '$this->soapClient->getAgentWithId(\'peter\', 999444)', $response);

        $namesInfo = new stdClass();
        $namesInfo->names = array('billy', 'clark');
        $namesInfo->id = 333;
        $response = $this->soapClient->namesForId($namesInfo);
        $this->_method('namesForId', '$this->soapClient->namesForId($namesInfo)', $response);
    }

}