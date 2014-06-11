<?php
namespace Clients;

use DOMDocument;
use SoapClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
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

    protected function method($method, $response)
    {
        $this->separator();

        $this->output->writeln('Method <info>' . $method . '</info>:');
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

    protected function separator()
    {
        return $this->output->writeln("\n---------\n");
    }

    protected function renderMethodsTable()
    {
        $table = $this->getHelper('table');
        $table->setHeaders(array('Method name'));
        $table->setRows($this->_getRows());
        $table->render($this->output);
    }

    private function _getRows()
    {
        return array_map(function ($function) {
            return array($function);
        }, $this->soapClient->__getFunctions());
    }

    protected function serviceInfo($name)
    {
        $style = new OutputFormatterStyle('red', 'green', array('bold'));
        $this->output->getFormatter()->setStyle('header', $style);
        $this->output->writeln("<header>\n\t$name\n</header>");
    }
}