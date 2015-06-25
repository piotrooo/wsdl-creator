<?php
/**
 * MockClass
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
namespace Mocks;

use stdClass;

class MockClass
{
    public function __construct()
    {
    }

    public function __destruct()
    {
    }

    public function __get($name)
    {
    }

    /**
     * @desc MethodParser to logging
     * @param string $message
     */
    private function _toLog($message)
    {
        file_put_contents('/tmp/logs_soap.log', $message);
    }

    public function getConstValue()
    {
        return 2;
    }

    /**
     * @WebMethod
     * @desc to sum two integers
     * @param int $a
     * @param int $b
     * @return int
     */
    public function sum($a, $b)
    {
        return $a + $b;
    }

    /**
     * @WebMethod
     * @param object $object1 @string=$name @int=$id
     * @return object $return @string=$new_name @int=$new_id
     */
    public function arrayTest($object1)
    {
        $o = new stdClass();
        $o->new_name = 'new:' . $object1->name;
        $o->new_id = $object1->id + 2;
        return $o;
    }

    /**
     * @WebMethod
     * @param wrapper $wrap @className=\Mocks\MockUserWrapper
     * @return bool
     */
    public function methodWithWrapper($wrap)
    {
        return $wrap ? true : false;
    }

    /**
     * @WebMethod
     * @return wrapper[] $mockUsers @className=\Mocks\MockUserWrapper
     */
    public function arrayOfMockUser()
    {
        $mockUsers = array();

        $o = new MockUserWrapper();
        $o->id = 1;
        $o->name = 'Fred';
        $o->age = 20;
        $mockUsers[] = $o;

        $o = new MockUserWrapper();
        $o->id = 2;
        $o->name = 'Murray';
        $o->age = 25;
        $mockUsers[] = $o;

        return $mockUsers;
    }

    /**
     * @WebMethod
     * noReturnFunction
     *
     * @param int $a
     */
    public function noReturnFunction($a)
    {
        $a = $a + 1;
    }

    /**
     * @WebMethod
     * voidReturnFunction
     *
     * @param int $a
     * @return void
     */
    public function voidReturnFunction($a)
    {
        $a = $a + 1;
    }
}
