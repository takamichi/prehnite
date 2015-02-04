<?php
namespace Prehnite;

/**
 * Optional
 *
 * @package Prehnite
 */
class Optional
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
     * 値が存在し、指定された述語に一致する場合は値を含むOptionalインスタンスを返し、
     * それ以外の場合は空のOptionalインスタンスを返します。
     * @param callable $predicate 述語評価(真偽を返す)を行うクロージャ
     * @return \Prehnite\Optional 値がフィルタ条件を満たす場合は値のOptional、それ以外の場合は空のOptional
     */
    public function filter(callable $predicate)
    {
        return $predicate($this->value) === true ? $this : self::ofEmpty();
    }

    /**
     * 値が存在する場合、指定されたマッピング関数を値に適用し、結果が null でなければ結果の値を含むOptionalインスタンスを返します。
     * それ以外の場合は空のOptionalインスタンスを返します。
     * @param callable $mapper 存在する値に適用するマッピング関数(値を返す)
     * @return \Prehnite\Optional 存在する値にマッピング関数を適用した結果値を含むOptional、
     *                            それ以外の場合は空のOptional
     * @throws \Prehnite\NullPointerException マッピング関数が null の場合
     */
    public function map(callable $mapper)
    {
        if ($this->value === null) {
            return self::ofEmpty();
        }

        if ($mapper === null) {
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
     */
    public function ifPresent(callable $callback)
    {
        if ($this->value === null) {
            return;
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
     * @param callable $callback 値が存在しないときに呼び出す
     * @return mixed 存在する値、それ以外の場合は $callback の結果
     * @throws \Prehnite\NullPointerException 値が存在せず、$callback が null の場合
     */
    public function orElseGet(callable $callback)
    {
        if ($this->value !== null) {
            return $this->value;
        }

        if ($callback === null) {
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
     * @param callable $callback 値が存在しないときに呼び出す
     * @return string 存在する値、それ以外の場合は $callback の結果の文字列
     */
    public function stringOrGet(callable $callback)
    {
        return (string)$this->orElseGet($callback);
    }

    public function __toString()
    {
        return $this->stringOr('');
    }
}
