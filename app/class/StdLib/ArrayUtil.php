<?php
/**
 * 配列関連ユーティリティ
 *
 * @package   StdLib
 * 
 */

namespace GyazoHj\StdLib;

/**
 * 配列関連ユーティリティ
 *
 * @package   StdLib
 * 
 */
class ArrayUtil extends \GyazoHj\StdLib\ObjectAbstract
{
    public static function pickup(array $arr, $name)
    {
        $retval = array();

        foreach ($arr as $key => $val)
        {
            if (is_array($val))
            {
                if (array_key_exists($name, $val))
                {
                    $retval[$key] = $val[$name];
                }
            }
            else if ($val instanceof \ArrayAccess)
            {
                if ($val->offsetExists($name))
                {
                    $retval[$key] = $val->offsetGet($name);
                }
            }
            else if (is_object($val))
            {
                if (property_exists($val, $name))
                {
                    $retval[$key] = $val->$name;
                }
            }
        }

        return $retval;
    }

    /**
     * 配列が連想配列か(文字キーがあるか)調べる。
     * '0'と0は同一視する。シーケンシャルじゃないと連想配列とみなされる。
     * json_encodeしたとき[]になるか{}になるかが目安になる。
     *
     * @param array $array 調べる配列
     * @return bool 連想配列ならtrue
     */
    public static function isHash(array $array)
    {
        $i = 0;
        foreach ($array as $k => $dummy)
        {
            if ($k !== $i++)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * array_reduce の key も渡される版
     *
     * @param array    $array 対象配列
     * @param callable $callback コールバック関数
     * @param mixed    $initial 初期値
     * @return array   array_reduce 結果
     */
    public static function reduceWithKey(array $array, callable $callback, $initial = null)
    {
        $result = $initial;

        foreach ($array as $k => $v)
        {
            $result = $callback($result, $k, $v);
        }

        return $result;
    }

    /**
     * 配列のキーと値を指定文字で連結した配列を返す。
     * $glueが指定されていたらさらにimplodeして返す。
     *
     * @param array $array 対象配列
     * @param string $separator 区切り文字
     * @param string $glue implode区切り文字
     * @return array $glueが指定されていたら文字列、されてなかったら単純配列
     */
    public static function joinKeyValue(array $array, $separator, $glue = null)
    {
        $result = array();

        foreach ($array as $k => $v)
        {
            $result[] = $k . $separator . $v;
        }

        if ($glue === null)
        {
            return $result;
        }
        else
        {
            return implode($glue, $result);
        }
    }

    /**
     * 位置を指定して配列に新しく追加する
     * @param array $array 対象配列
     * @param int $position 挿入位置
     * @param mixed $value 値
     * @param string $key キー
     * @return array $array引数値
     */
    public static function insert(array &$array, $position, $value, $key = null)
    {
        if ($key == null)
        {
            $key = $position;
        }

        $count = count($array);
        if ($position < 0)
        {
            $position = 0;
        }
        else if ($position > $count)
        {
            $position = $count;
        }

        $head = array_slice($array, 0, $position);
        $body = array($key => $value);
        $tail = array_slice($array, $position);

        $array = array_merge($head, $body, $tail);
        return $array;
    }

    /**
     * キーを指定して配列の要素を削除する。
     * unsetと同義だが配列で複数指定できる。
     * また、unsetされた値を返り値として返す。取り出した後unsetしたいときに使う。
     *
     * @param array $array 対象配列
     * @param string|array $key 削除するキー
     * @return mixed unsetされた値。$keyが配列なら配列。単値なら単値。消すものがなかったらnull
     */
    public static function unsetKey(array &$array, $key)
    {
        $keys = (array) $key;

        $result = array();

        foreach ($keys as $k)
        {
            if (!array_key_exists($k, $array))
            {
                continue;
            }

            $result[$k] = $array[$k];
            unset($array[$k]);
        }

        if (count($result) == 0)
        {
            return null;
        }
        else if (is_array($key))
        {
            return $result;
        }
        else
        {
            return current($result);
        }
    }

    /**
     * 値を指定して配列の要素を削除する.
     * 配列による$valueの複数指定はできない。なぜなら「複数指定したい」のか「そういう配列の要素を指定したい」のか区別できないから。
     *
     * @param array $array 対象配列
     * @param mixed $value 削除するキーの値
     * @param bool $strict 検索時に厳密な比較 (===) を行うかどうか。
     * @return array 削除されたキーを配列で返す
     */
    public static function unsetValue(array &$array, $value, $strict = false)
    {
        $keys = array_keys($array, $value, $strict);
        self::unsetKey($array, $keys);
        return $keys;
    }

    /**
     * array_filterのキー版。なぜか標準にない。
     * $callbackに呼び出し可能でない文字列を渡すとevalしてフィルタされる
     *
     * @param array $input 対象配列
     * @param mixed $callback コールバック関数
     * @return array filterされた配列
     */
    public static function filterKey($input, $callback)
    {
        $callable = is_callable($callback);

        if (!$callable && !is_string($callback))
        {
            throw new \InvalidArgumentException('$callback is not callable');
        }

        $result = array();

        foreach ($input as $key => $value)
        {
            if (($callable && $callback($key)) || (!$callable && eval("return ({$callback});")))
            {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * 連想配列の配列からキー名を指定して新しく配列を生成する。
     * 値名を指定するとPDO::FETCH_KEY_PAIR的な動作になる。
     * 値名を省略するとPDO::FETCH_ASSOC的な動作になる。
     *
     * @param array $input 対象配列
     * @param string $keyName キー名
     * @param string $valueName 値名
     * @return array $keyNameをキーとする連想配列。$valueNameが指定されているならシンプルなkey=>valueな配列になる
     */
    public static function associate($input, $keyName, $valueName = null)
    {
        $result = array();

        foreach ($input as $v)
        {
            if (!is_array($v))
            {
                throw new \DomainException("\$input is not array of hash_array");
            }

            if (!array_key_exists($keyName, $v))
            {
                throw new \OutOfRangeException("{$keyName} is not found");
            }

            if ($valueName === null)
            {
                $key = $v[$keyName];
                $val = $v;
            }
            else
            {
                $key = $v[$keyName];
                $val = $v[$valueName];
            }

            $result[$key] = $val;
        }

        return $result;
    }

    /**
     * 連想配列の配列からキー名を指定して階層化する
     * キー名に配列を渡すと多階層構造の配列になる
     *
     * @param array $input 対象配列
     * @param string|array $keyNames キー名。配列可
     * @return array $keyNamesの値をキーとする階層配列。
     */
    public static function stratificate($input, $keyNames)
    {
        //指定された配列で順繰りにキーを作成するメインルーチン
        $routine = function (&$array, $keys, $value) use (&$routine)
        {
            if (!is_array($value))
            {
                throw new \DomainException("\$value is not array of hash_array");
            }

            $key = array_shift($keys);

            if (!array_key_exists($key, $value))
            {
                throw new \OutOfRangeException("{$key} is not found");
            }

            $kv = $value[$key];

            //最後だったら配列ではなく値となる
            if (count($keys) == 0)
            {
                $array[$kv][] = $value;
            }
            //最後じゃないなら再帰
            else
            {
                $routine($array[$kv], $keys, $value);
            }
        };

        /// ここから本処理

        if (!is_array($keyNames))
        {
            $keyNames = array(
                $keyNames
            );
        }

        $result = array();

        foreach ($input as $row)
        {
            $routine($result, $keyNames, $row);
        }

        return $result;
    }

    /**
     * デフォルト機能付き配列アクセス。キーがなかったらデフォルト値を返す。
     * 主に$_GETや$_POSTでの使用を想定。
     *
     * @param array $input 入力配列
     * @param string $key キー
     * @param mixed $default デフォルト値
     * @return mixed 配列の値
     */
    public static function get($search, $key, $default = null)
    {
        // このように書きたくない場合に
        // $v = array_key_exists($key, $search) ? $search[$key] : $default;
        // このように使う。
        // $v = ArrayUtil::get($search, $key, $default);

        if (array_key_exists($key, $search))
        {
            return $search[$key];
        }

        return $default;
    }

    /**
     * 変数を配列に変換する
     *
     * 配列やイテレータの場合のみ成功する。失敗時には例外。
     *
     * @param array $input
     * @param string $func
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    private static function _convertArray($input, $func = null, $name = null)
    {
        if (is_array($input))
        {
            return $input;
        }
        else if ($input instanceof \Iterator)
        {
            return iterator_to_array($input);
        }
        else
        {
            $func = $func !== null ? $func : __METHOD__;
            $name = $name !== null ? $name : 'argument';
            $type = is_object($input) ? get_class($input) : gettype($input);
            throw new \InvalidArgumentException("$func() $name should be array or iterator, but $type given.");
        }
    }

    /**
     * 配列の指定個数の要素の組み合わせを全て生成する
     *
     * $input の n 個の要素の組み合わせの全パターンを取得する。
     *
     *     e.g.)
     *       self::combination(2, array(1, 2, 3, 4));
     *       // array([1, 2], [1, 3], [1, 4], [2, 3], [2, 4], [3, 4]);
     *
     * $callback が指定された場合は戻り値は null となり、代わりに $callback が組み合わせの数だけ呼び出される。
     *
     * @param array $input
     * @param integer $n
     * $param callable $callback
     * @return array|null
     */
    public static function combination($input, $n, $callback = null)
    {
        $input = self::_convertArray($input, __METHOD__, '$input');
        $input = array_values($input);

        $ret = null;

        if (!$callback)
        {
            $ret = array();
            $callback = function ($arr) use (&$ret) {
                $ret[] = $arr;
            };
        }

        $len = count($input);

        if ($n < 0 || $len < $n)
        {
            // noop
        }
        else if ($n == 0)
        {
            $callback(array());
        }
        else
        {
            self::_combination(array(), $input, $len, $n - 1, $callback);
        }

        return $ret;
    }

    /**
     * 配列の全ての要素の組み合わせを取得する
     *
     * $input の全ての要素の組み合わせの全パターンを取得する。
     *
     *     e.g.)
     *       self::combination(array(1, 2, 3));
     *       // array([], [1], [2], [3], [1, 2], [1, 3], [2, 3], [1, 2, 3]);
     *
     * $callback が指定された場合は戻り値は null となり、代わりに $callback が組み合わせの数だけ呼び出される。
     *
     * @param array $input
     * $param callable $callback
     * @return array|null
     */
    public static function combinationAll($input, $callback = null)
    {
        $input = self::_convertArray($input, __METHOD__, '$input');
        $input = array_values($input);

        $ret = null;

        if (!$callback)
        {
            $ret = array();
            $callback = function ($arr) use (&$ret) {
                $ret[] = $arr;
            };
        }

        $callback(array());
        self::_combinationAll(array(), $input, count($input), $callback);

        return $ret;
    }

    private static function _combination($work, $input, $i, $n, $callback)
    {
        while ($i > 0)
        {
            $tmp = $work;
            $tmp[] = $input[--$i];

            if ($n)
            {
                self::_combination($tmp, $input, $i, $n - 1, $callback);
            }
            else
            {
                $callback($tmp);
            }
        }
    }

    private static function _combinationAll($work, $input, $i, $callback)
    {
        while ($i > 0)
        {
            $tmp = $work;
            $tmp[] = $input[--$i];

            $callback($tmp);
            self::_combinationAll($tmp, $input, $i, $callback);
        }
    }
}
