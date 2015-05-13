<?php
/**
 * オブジェクトクラス
 *
 * @package   StdLib
 * 
 */

namespace GyazoHj\StdLib;

/**
 * オブジェクトクラス
 *
 * @package   StdLib
 * 
 */
abstract class ObjectAbstract
{
    /**
     * クラス名を返す
     *
     * @return string
     */
    public static function classof()
    {
        return get_called_class();
    }

    public function __call($name, array $arguments)
    {
        throw new \LogicException(sprintf('Undefined Method: %s->%s', get_class($this), $name));
    }

    public function __get($name)
    {
        throw new \LogicException(sprintf('Undefined Property: %s->%s', get_class($this), $name));
    }

    public function __set($name, $value)
    {
        throw new \LogicException(sprintf('Undefined Property: %s->%s', get_class($this), $name));
    }

    public function __isset($name)
    {
        throw new \LogicException(sprintf('Undefined Property: %s->%s', get_class($this), $name));
    }

    public function __unset($name)
    {
        throw new \LogicException(sprintf('Undefined Property: %s->%s', get_class($this), $name));
    }

    public function __clone()
    {
        throw new \LogicException(sprintf('not impl clone method: %s', get_class($this)));
    }

    public function __toString()
    {
        return get_class($this);
    }
}
