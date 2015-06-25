<?php
/**
 * MockClassMultipleNamespace
 *
 * @author Piotr Olaszewski <piotroo89@gmail.com>
 */
namespace Mocks\Test;

use stdClass;

class MockClassMultipleNamespace
{
    /**
     * @desc MethodParser to logging
     * @param string $message
     */
    private function _toLog($message)
    {
        file_put_contents('/tmp/logs_soap.log', $message);
    }

    /**
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
}
