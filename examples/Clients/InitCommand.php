<?php
namespace Clients;

use DOMDocument;
use SoapClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    /**
     * @var OutputInterface
     */
    protected $output;
    /**
     * @var SoapClient
     */
    protected $soapClient;

    protected function _tableOfMethods()
    {
        $table = $this->getHelper('table');
        $table->setHeaders(array('Methods'));
        $table->setRows($this->_getMethods());
        $table->render($this->output);
    }

    protected function _getMethods()
    {
        return array_map(function ($function) {
            return array($function);
        }, $this->soapClient->__getFunctions());
    }

    protected function _method($method, $call, $response)
    {
        $this->_separator();

        $this->output->writeln('Method <info>' . $method . '</info>:');
        $this->output->writeln('');

        $this->output->writeln('<comment>Call:</comment>');
        $this->output->writeln($call);
        $this->output->writeln('');

        $this->output->writeln('<comment>Response:</comment>');
        print_r($response);
        $this->output->writeln('');
        $this->output->writeln('');

        $DOMDocument = new DOMDocument();
        $DOMDocument->formatOutput = true;

        $this->output->writeln('<comment>Request headers:</comment>');
        $DOMDocument->loadXML($this->soapClient->__getLastRequest());
        $this->output->writeln($DOMDocument->saveXML());

        $this->output->writeln('<comment>Response headers:</comment>');
        $DOMDocument->loadXML($this->soapClient->__getLastResponse());
        $this->output->writeln($DOMDocument->saveXML());
    }

    protected function _separator()
    {
        return $this->output->writeln("\n---------\n");
    }
}