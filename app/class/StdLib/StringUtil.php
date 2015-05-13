<?php
/**
 * 文字列関連ユーティリティ
 *
 * @package   StdLib
 */

namespace GyazoHj\StdLib;

/**
 * 文字列関連ユーティリティ
 *
 * @package   StdLib
 */
class StringUtil extends \GyazoHj\StdLib\ObjectAbstract
{
    /**
     * 全角対応trim
     *
     * @param string $string
     */
    public static function trim($string)
    {
        return preg_replace('/^[\s　]+|[\s　]+$/u', '', $string);
    }

    /**
     * 空文字か判定する
     *
     * @param string $string
     */
    public static function isEmpty($string)
    {
        return (strlen($string) === 0);
    }

    /**
     * 空白文字のみか判定する
     *
     * @param string $string
     */
    public static function isBlank($string)
    {
        return !!preg_match('/^[\s　]*$/u', $string);
    }

    /**
     * 連想配列を指定できるようにした vsprintf
     *
     * @param string $format フォーマット文字列
     * @param array $array フォーマット引数
     * @return string フォーマットされた文字列
     * @throws \OutOfBoundsException
     */
    public static function ksprintf($format, array $array)
    {
        $keys = array_flip(array_keys($array));
        $vals = array_values($array);

        $format = preg_replace_callback('/%%|%(.*?)\$/u', function ($m) use ($keys) {

            if (!isset($m[1]))
            {
                return $m[0];
            }

            $w = $m[1];

            if (!isset($keys[$w]))
            {
                throw new \OutOfBoundsException("ksprintf(): Undefined index: $w");
            }

            return '%' . ($keys[$w] + 1) . '$';

        }, $format);

        return vsprintf($format, $vals);
    }
}
