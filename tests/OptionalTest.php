<?php
namespace PrehniteTest;

use Prehnite\Object;
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

    /** @test */
    public function of_Optionalの多重ラップをしない()
    {
        $result = Optional::of(Optional::ofEmpty());
        $this->assertEquals('empty', $result->orElse('empty'));
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
    public function filter_評価で真のとき値を含むOptionalを返す()
    {
        $result = Optional::of(100)->filter(function ($value){
            return ($value === 100);
        });
        $this->assertTrue($result->isPresent());
        $this->assertEquals(100, $result->get());
    }

    /** @test */
    public function filter_評価で偽のとき空のOptionalを返す()
    {
        $result = Optional::of(100)->filter(function ($value) {
            return ($value > 111);
        });
        $this->assertFalse($result->isPresent());
    }

    /** @test */
    public function filter_値が空のとき空のOptionalを返す()
    {
        $result = Optional::ofEmpty()->filter(function ($value) {
            return ($value === null);
        });
        $this->assertFalse($result->isPresent());
    }

    /** @test */
    public function map_値が存在し結果があるとき値のOptionalを返す()
    {
        $result = Optional::of(100)->map(function ($value) {
            return $value * 2;
        })->orElse(0);
        $this->assertEquals(200, $result);
    }

    /** @test */
    public function map_値が存在するが結果がnullのとき空のOptionalを返す()
    {
        $result = Optional::of(100)->map(function ($value) {
            return null;
        })->isPresent();
        $this->assertFalse($result);
    }

    /** @test */
    public function map_値が存在しないとき空のOptionalを返す()
    {
        $result = Optional::ofEmpty()->map(function ($value) {
            return $value * 100;
        })->isPresent();
        $this->assertFalse($result);
    }

    /**
     * @test
     * @expectedException \Prehnite\NullPointerException
     */
    public function map_呼び出せないマッピング関数のとき例外をスローする()
    {
        Optional::of(123)->map('')->isPresent();
    }

    /** @test */
    public function get_値を取得する()
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
    public function ifPresent_値が存在するときcallbackを呼び出す()
    {
        $result = false;
        Optional::of(123)->ifPresent(function ($value) use (&$result) {
            $result = true;
        });
        $this->assertTrue($result);
    }

    /** @test */
    public function ifPresent_値が存在しないとき何もしない()
    {
        $result = false;
        Optional::ofEmpty()->ifPresent(function ($value) use (&$result) {
            $result = true;
        });
        $this->assertFalse($result);
    }

    /**
     * @test
     * @expectedException \Prehnite\NullPointerException
     */
    public function ifPresent_callbackを呼び出せないとき例外をスローする()
    {
        Optional::of(123)->ifPresent('');
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
    public function orElseGet_値があるとき値を返す()
    {
        $result = Optional::of(123)->orElseGet(function () {
            return -100;
        });
        $this->assertEquals(123, $result);
    }

    /** @test */
    public function orElseGet_値がないときcallbackを呼び出す()
    {
        $result = Optional::ofEmpty()->orElseGet(function () {
            return -100;
        });
        $this->assertEquals(-100, $result);
    }

    /**
     * @test
     * @expectedException \Prehnite\NullPointerException
     */
    public function orElseGet_callbackを呼び出せないとき例外をスローする()
    {
        Optional::ofEmpty()->orElseGet('');
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
    public function __toString_文字列を返す()
    {
        $result = (string)Optional::of(123);
        $this->assertEquals('123', $result);
    }

    /** @test */
    public function equals_値が等しいことを確認する()
    {
        $result = Optional::of(123)->equals(Optional::of(123));
        $this->assertTrue($result);
    }

    /** @test */
    public function equals_値がObject継承のとき等しいことを確認する()
    {
        $result = Optional::of(new Object())->equals(Optional::of(new Object()));
        $this->assertTrue($result);
    }

    /** @test */
    public function equals_値が等しくないことを確認する()
    {
        $result = Optional::of(123)->equals(Optional::ofEmpty());
        $this->assertFalse($result);
    }

    /** @test */
    public function equals_クラスが等しくないことを確認する()
    {
        $result = Optional::of(123)->equals(new Object());
        $this->assertFalse($result);
    }
}
