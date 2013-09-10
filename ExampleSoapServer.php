<?php
require_once 'vendor/autoload.php';

use WSDL\WSDLCreator;

if (isset($_GET['wsdl'])) {
    $wsdl = new WSDL\WSDLCreator('ExampleSoapServer', 'http://localhost/wsdl-creator/ExampleSoapServer.php');
    $wsdl->setNamespace("http://foo.bar/");
    $wsdl->renderWSDL();
    exit;
}

$server = new SoapServer(NULL, array(
    'uri' => 'http://localhost/wsdl-creator/ExampleSoapServer.php'
));
$server->setClass('ExampleSoapServer');
$server->handle();

class UserWrapper
{
    /**
     * @type int
     */
    public $id;
    /**
     * @type string
     */
    public $name;
    /**
     * @type int
     */
    public $age;
}

class ExampleSoapServer
{
    /**
     * @desc to sum two integers
     * @param int $a
     * @param int $b
     * @return int $return
     */
    public function sum($a, $b)
    {
        return $a + $b;
    }

    /**
     * @param object $object1 @string=name @int=id
     * @return object $return @string=new_name @int=new_id
     */
    public function arrayTest($object1)
    {
        $o = new stdClass();
        $o->new_name = 'new:' . $object1->name;
        $o->new_id = $object1->id + 2;
        return $o;
    }

    /**
     * @param wrapper $wrapper @className=UserWrapper
     * @return object $return @string=name @int=age
     */
    public function userWrapper($wrapper)
    {
        $o = new stdClass();
        $o->name = $wrapper->name;
        $o->age = $wrapper->age;
        return $o;
    }

    /**
     * @param string[] $strings
     * @return int[] $return
     */
    public function stringsMethod($strings)
    {
        $int = array();
        foreach ($strings as $i => $string) {
            $int[] = $i + 1;
        }
        return $int;
    }

    /**
     * @param wrapper[] $strings @className=UserWrapper
     * @return bool $return
     */
    public function usersMethod($strings)
    {
        $int = array();
        foreach ($strings as $i => $string) {
            $int[] = $i + 1;
        }
        return $int;
    }

    /**
     * @param object[] $object1 @string=name @int=id
     * @return wrapper $return @className=UserWrapper
     */
    public function objectsTest($object1)
    {
        $o = new stdClass();
        $o->new_name = 'new:' . $object1->name;
        $o->new_id = $object1->id + 2;
        return $o;
    }
}