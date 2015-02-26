<?php
namespace PrehniteTest;

use Prehnite\Object;

class ObjectTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function magic_toString()
    {
        $object = new Object();
        $result = (string)$object;
        $this->assertEquals(Object::class, $result);
    }

    /** @test */
    public function equals_等しいときtrue()
    {
        $object1 = new Object();
        $object2 = new Object();
        $this->assertTrue($object1->equals($object2));
    }

    /** @test */
    public function equals_等しくないときfalse()
    {
        $result = (new Object())->equals(123);
        $this->assertFalse($result);
    }
}
