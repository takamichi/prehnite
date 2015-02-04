<?php
namespace PrehniteTest;

use Prehnite\Optional;

class OptionalTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function ofEmpty()
    {
        $result = Optional::ofEmpty();
        $this->assertInstanceOf(Optional::class, $result);
        $this->assertFalse($result->isPresent());
    }

    /** @test */
    public function of()
    {
        $result = Optional::of(123);
        $this->assertInstanceOf(Optional::class, $result);
        $this->assertEquals(123, $result->get());
    }

    /**
     * @test
     * @expectedException \Prehnite\NullPointerException
     */
    public function of_nullを許容しない()
    {
        Optional::of(null);
    }

    /** @test */
    public function ofNullable()
    {
        $result = Optional::ofNullable(123);
        $this->assertInstanceOf(Optional::class, $result);
        $this->assertEquals(123, $result->get());
    }

    /** @test */
    public function ofNullable_nullを許容し空のOptionalを返す()
    {
        $result = Optional::ofNullable(null);
        $this->assertInstanceOf(Optional::class, $result);
        $this->assertFalse($result->isPresent());
    }

    /** @test */
    public function get()
    {
        $result = Optional::of('lorem ipsum');
        $this->assertEquals('lorem ipsum', $result->get());
    }

    /**
     * @test
     * @expectedException \Prehnite\NoSuchElementException
     */
    public function get_値がないときに例外をスローする()
    {
        $result = Optional::ofEmpty();
        $result->get();
    }

    /** @test */
    public function isPresent_値があるときtrue()
    {
        $result = Optional::of(123);
        $this->assertTrue($result->isPresent());
    }

    /** @test */
    public function isPresent値がないときfalse()
    {
        $result = Optional::ofNullable(null);
        $this->assertFalse($result->isPresent());
    }

    /** @test */
    public function orElse_値があるとき値を返す()
    {
        $result = Optional::of(123)->orElse(999);
        $this->assertEquals(123, $result);
    }

    /** @test */
    public function orElse_値がないとき指定値を返す()
    {
        $result = Optional::ofEmpty()->orElse(100);
        $this->assertEquals(100, $result);
    }

    /** @test */
    public function orElseThrow_値があるとき値を返す()
    {
        $result = Optional::of(123)->orElseThrow(new \InvalidArgumentException());
        $this->assertEquals(123, $result);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function orElseThrow_値がないとき指定の例外をスローする()
    {
        Optional::ofEmpty()->orElseThrow(new \InvalidArgumentException());
    }

    /** @test */
    public function stringOr_値があるとき値の文字列を返す()
    {
        $result = Optional::of(123)->stringOr('000');
        $this->assertTrue(is_string($result));
        $this->assertEquals('123', $result);
    }

    /** @test */
    public function stringOr_値がないとき引数の文字列を返す()
    {
        $result = Optional::ofEmpty()->stringOr(123);
        $this->assertTrue(is_string($result));
        $this->assertEquals('123', $result);
    }
}
