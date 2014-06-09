<?php
namespace Clients\RpcEncoded;

use Clients\InitCommand;
use SoapClient;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SimpleCommand extends InitCommand
{
    protected function configure()
    {
        $this->setName('clients:rpc_encoded_simple');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->soapClient = new SoapClient('http://localhost/wsdl-creator/examples/rpc_literal/SimpleExampleSoapServer.php?wsdl', array(
            'trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE, 'features' => SOAP_SINGLE_ELEMENT_ARRAYS | SOAP_USE_XSI_ARRAY_TYPE
        ));
        $this->_tableOfMethods();

//        $response = $this->soapClient->getNameWithAge('john', 5);
//        $this->_method('getNameWithAge', '$soapClient->getNameWithAge(\'john\', 5)', $response);

        $response = $this->soapClient->getNameForUsers(array('john', 'billy', 'peter'));
        $this->_method('getNameForUser', $response);

//        $response = $this->soapClient->countTo(5);
//        $this->_method('countTo', '$soapClient->countTo(5)', $response);
    }
}