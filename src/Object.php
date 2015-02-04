<?php
namespace Prehnite;

/**
 * Object
 *
 * @package Prehnite
 */
class Object
{
    /**
     * オブジェクトを文字列に変換します。
     * @return string オブジェクト文字列
     */
    public function __toString()
    {
        return static::class;
    }

    /**
     * 2つのオブジェクトのインスタンスが等しいかどうかを判断します。
     * @param mixed $value
     * @return bool 指定したオブジェクトが現在のオブジェクトと等しい場合は true。それ以外の場合は false。
     */
    public function equals($value)
    {
        if (!$value instanceof static) {
            return false;
        }

        return ((string)$this === (string)$value);
    }
}
