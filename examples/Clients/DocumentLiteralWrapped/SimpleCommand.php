<?php
namespace Clients\DocumentLiteralWrapped;

use Clients\InitCommand;
use SoapClient;
use stdClass;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SimpleCommand extends InitCommand
{
    protected function configure()
    {
        $this->setName('document_literal:simple');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->soapClient = new SoapClient('http://localhost/wsdl-creator/examples/document_literal_wrapped/SimpleExampleSoapServer.php?wsdl', array(
            'trace' => true, 'cache_wsdl' => WSDL_CACHE_NONE
        ));

        $this->serviceInfo('Client Simple - document/literal wrapped');

        $this->renderMethodsTable();

        $params = new stdClass();
        $params->name = 'john';
        $params->age = 5;
        $response = $this->soapClient->getNameWithAge($params);
        $this->method('getNameWithAge', array($params), $response);

        $params = new stdClass();
        $params->names = array('john', 'billy', 'peter');
        $response = $this->soapClient->getNameForUsers($params);
        $this->method('getNameForUser', array($params), $response);

        $params = new stdClass();
        $params->max = 5;
        $response = $this->soapClient->countTo($params);
        $this->method('countTo', array($params), $response);
    }
}