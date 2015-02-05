<?php
namespace Prehnite;

/**
 * Optional
 *
 * @package Prehnite
 */
class Optional extends Object
{
    /** @type Optional|null */
    private static $empty;

    /** @type mixed|null */
    private $value = null;

    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Optionalクラスの空のインスタンスを返します。
     * @return \Prehnite\Optional 空のOptional
     */
    public static function ofEmpty()
    {
        self::initEmpty();
        return self::$empty;
    }

    private static function initEmpty()
    {
        if (self::$empty === null) {
            self::$empty = new self(null);
        }
    }

    /**
     * 指定された値を含むOptionalクラスのインスタンスを返します。
     * @param mixed $value 存在する値(nullを許容しない)
     * @return \Prehnite\Optional $value を含むOptional
     * @throws \Prehnite\NullPointerException $value が null の場合
     */
    public static function of($value)
    {
        if ($value === null) {
            throw new NullPointerException;
        }

        // 多重ラップを回避する
        if ($value instanceof self) {
            return $value;
        }

        return new static($value);
    }

    /**
     * 指定された値が null でない場合に値を含むOptionalクラスのインスタンスを返し、
     * それ以外の場合は空のOptionalインスタンスを返します。
     * @param mixed $value 値(nullを許容する)
     * @return \Prehnite\Optional 値を含むOptional、それ以外の場合は空のOptional
     */
    public static function ofNullable($value)
    {
        return ($value === null) ? self::ofEmpty() : self::of($value);
    }

    /**
     * 値が存在し、$callback評価条件が真なら値を含むOptionalインスタンスを返し、
     * それ以外の場合は空のOptionalインスタンスを返します。
     * @param callable $callback 値を評価し、真偽を返す
     * @return \Prehnite\Optional 値が評価条件を満たす場合は値を含むOptional、それ以外の場合は空のOptional
     * @throws \Prehnite\NullPointerException $callback が呼び出せない場合
     */
    public function filter($callback)
    {
        if ($this->value === null) {
            return self::ofEmpty();
        }

        if (!is_callable($callback)) {
            throw new NullPointerException;
        }

        return $callback($this->value) === true ? $this : self::ofEmpty();
    }

    /**
     * 値が存在する場合 $callback を値に適用し、結果が null でなければ結果の値を含むOptionalインスタンスを返します。
     * それ以外の場合は空のOptionalインスタンスを返します。
     * @param callable $mapper 値に適用し、結果を返す
     * @return \Prehnite\Optional 存在する値にマッピング関数を適用した結果値を含むOptional、
     *                            それ以外の場合は空のOptional
     * @throws \Prehnite\NullPointerException $callback が呼び出せない場合
     */
    public function map($mapper)
    {
        if ($this->value === null) {
            return self::ofEmpty();
        }

        if (!is_callable($mapper)) {
            throw new NullPointerException;
        }

        return self::ofNullable($mapper($this->value));
    }

    /**
     * 値が存在する場合は値を返し、それ以外の場合は NoSuchElementException をスローします。
     * @return mixed 存在する値
     * @throws \Prehnite\NoSuchElementException 値が存在しない場合
     */
    public function get()
    {
        if ($this->value === null) {
            throw new NoSuchElementException;
        }

        return $this->value;
    }

    /**
     * 値が存在する場合は $callback をその値で呼び出し、それ以外の場合は何も行いません。
     * @param callable $callback 値が存在するときに呼び出す
     * @return void
     * @throws \Prehnite\NullPointerException 値が存在するが、$callback が呼び出せない場合
     */
    public function ifPresent($callback)
    {
        if ($this->value === null) {
            return;
        }

        if (!is_callable($callback)) {
            throw new NullPointerException;
        }

        $callback($this->value);
    }

    /**
     * 値が存在するか判断します。
     * @return bool 存在する値がある場合は true。それ以外の場合は false。
     */
    public function isPresent()
    {
        return $this->value !== null;
    }

    /**
     * 値が存在する場合は値を返し、それ以外の場合は引数の値を返します。
     * @param mixed $value 値が存在しないときに返す値(nullを許容する)
     * @return mixed 存在する値、それ以外の場合は $value
     */
    public function orElse($value)
    {
        return ($this->value === null) ? $value : $this->value;
    }

    /**
     * 値が存在する場合は値を返し、それ以外の場合は $callback の呼び出し結果を返します。
     * @param callable $callback 値が存在しないときに呼び出し、値を返す
     * @return mixed 存在する値、それ以外の場合は $callback の結果
     * @throws \Prehnite\NullPointerException 値が存在せず、$callback が呼び出せない場合
     */
    public function orElseGet($callback)
    {
        if ($this->value !== null) {
            return $this->value;
        }

        if (!is_callable($callback)) {
            throw new NullPointerException;
        }

        return $callback();
    }

    /**
     * 値が存在する場合は値を返し、それ以外の場合は指定された例外をスローします。
     * @param \Exception $exception 値が存在しない場合にスローする例外
     * @return mixed 存在する値
     * @throws \Exception ($exception) 値が存在しない場合
     */
    public function orElseThrow(\Exception $exception)
    {
        if ($this->value === null) {
            throw $exception;
        }

        return $this->value;
    }

    /**
     * 値が存在する場合は値の文字列を返し、それ以外の場合は引数値の文字列を返します。
     * @param mixed $value 値が存在しないときに返す値
     * @return string 存在する値、それ以外の場合は $value の文字列
     */
    public function stringOr($value)
    {
        return (string)$this->orElse($value);
    }

    /**
     * 値が存在する場合は値の文字列を返し、それ以外の場合は $callback の呼び出し結果の文字列を返します。
     * @param callable $callback 値が存在しないときに呼び出し、値を返す
     * @return string 存在する値、それ以外の場合は $callback の結果の文字列
     */
    public function stringOrGet($callback)
    {
        return (string)$this->orElseGet($callback);
    }

    public function __toString()
    {
        return $this->stringOr('');
    }

    public function equals($value)
    {
        if (!$value instanceof self) {
            return false;
        }

        if ($this->value instanceof Object) {
            return $this->value->equals($value->value);
        }

        return ($this->value === $value->value);
    }
}
