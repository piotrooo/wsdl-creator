<?php
namespace Clients\RpcLiteral;

use Clients\InitCommand;
use SoapClient;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SimpleCommand extends InitCommand
{
    protected function configure()
    {
        $this->setName('clients:rpc_literal_simple');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->soapClient = new SoapClient('http://localhost/wsdl-creator/examples/rpc_literal/SimpleExampleSoapServer.php?wsdl', array(
            'uri' => "http://foo.bar/", 'location' => 'http://localhost/wsdl-creator/examples/rpc_literal/SimpleExampleSoapServer.php',
            'trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE
        ));

        $response = $this->soapClient->getNameWithAge('john', 5);
        $this->_method('getNameWithAge', $response);

        $response = $this->soapClient->getNameForUsers(array('john', 'billy', 'peter'));
        $this->_method('getNameForUser', $response);

        $response = $this->soapClient->countTo(5);
        $this->_method('countTo', $response);
    }
}