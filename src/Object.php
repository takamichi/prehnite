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
     * @param \Prehnite\Object $object
     * @return bool 指定したオブジェクトが現在のオブジェクトと等しい場合は true。それ以外の場合は false。
     */
    public function equals(Object $object)
    {
        if (!$object instanceof static) {
            return false;
        }

        return ((string)$this === (string)$object);
    }

    /**
     * 現在のオブジェクトのインスタンスを簡易的に複製し返します。
     * @return static インスタンスの簡易的な複製
     */
    final public function memberwiseClone()
    {
        return clone $this;
    }
}
