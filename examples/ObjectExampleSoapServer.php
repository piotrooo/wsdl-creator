<?php
require_once '../vendor/autoload.php';

use WSDL\WSDLCreator;

if (isset($_GET['wsdl'])) {
    $wsdl = new WSDL\WSDLCreator('ObjectSoapServer', 'http://localhost:8080/wsdl-creator/examples/ObjectExampleSoapServer.php');
    $wsdl->setNamespace("http://foo.bar/");
    $wsdl->renderWSDL();
    exit;
}

$server = new SoapServer(null, array(
    'uri' => 'http://localhost:8080/wsdl-creator/examples/ObjectExampleSoapServer.php'
));
$server->setClass('ObjectSoapServer');
$server->handle();

class Agent
{
    /**
     * @type string
     */
    public $name;
    /**
     * @type int
     */
    public $number;
}

class ObjectSoapServer
{
    /**
     * @param object $info @string=$name @int=$age
     * @return string $returnInfo
     */
    public function userInfo($info)
    {
        return 'Your name is: ' . $info->name . ' and you have ' . $info->age . ' years old';
    }

    /**
     * @param string $name
     * @param string $number
     * @return object $agentNameWithId @(wrapper $agent @className=Agent) @int=$id
     */
    public function getAgentWithId($name, $number)
    {
        $agent = new Agent();
        $agent->name = $name;
        $agent->number = $number;

        $return = new stdClass();
        $return->agent = $agent;
        $return->id = 3543456;
        return $return;
    }

    /**
     * @param object $namesInfo @string[]=$names @int=$id
     * @return string $namesForId
     */
    public function namesForId($namesInfo)
    {
        return '[#' . $namesInfo->id . '] Names: ' . implode(', ', $namesInfo->names);
    }

    /**
     * @return object[] $companies @string=$name @int=$id
     */
    public function getCompanies()
    {
        $companies = array();
        $companies[0] = new stdClass();
        $companies[0]->name = 'Example1';
        $companies[0]->id = '1';
        $companies[1] = new stdClass();
        $companies[1]->name = 'Example2';
        $companies[1]->id = '3';
        return $companies;
    }
}