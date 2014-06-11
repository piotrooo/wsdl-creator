<?php
namespace Clients\RpcLiteral;

use Clients\InitCommand;
use SoapClient;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WrapperCommand extends InitCommand
{
    protected function configure()
    {
        $this->setName('clients:rpc_literal_wrapper');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $this->soapClient = new SoapClient('http://localhost/wsdl-creator/examples/rpc_literal/WrapperExampleSoapServer.php?wsdl', array(
            'uri' => "http://foo.bar/", 'location' => 'http://localhost/wsdl-creator/examples/rpc_literal/WrapperExampleSoapServer.php',
            'trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE
        ));
    }
}