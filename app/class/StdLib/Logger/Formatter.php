<?php
/**
 *
 * 
 *
 */

namespace GyazoHj\StdLib\Logger;

class Formatter
{
    /**
     * ログメッセージの最大長
     *
     * @var int
     */
    const _MAX_MESSAGE_SIZE = 1024;

    /**
     * ログの改行を除去してバイトサイズの調整を行う
     *
     * @param string $message
     * @return string
     */
    public static function cutoff($message)
    {
        $message = strtr($message, array("\n" => " ", "\r" => " "));

        if (strlen($message) < self::_MAX_MESSAGE_SIZE)
        {
            return $message;
        }
        else
        {
            return mb_strcut($message, 0, self::_MAX_MESSAGE_SIZE);
        }
    }

    /**
     * ログの書式化
     *
     * @param string      $message   ログメッセージ
     * @param int         $priority  プライオリティ
     * @param int|null    $timestamp タイムスタンプ
     *
     * @return string
     */
    public static function format($message, $priority, $timestamp)
    {
        $timestamp = $timestamp ?: time();

        $date = date("Y/m/d\TH:i:s", $timestamp);

        $pstr = LogLevel::toString($priority);

        $pid = getmypid();

        $msg = sprintf("%s %s[%s]: %s" . PHP_EOL, $date, $pstr, $pid, $message);

        return $msg;
    }
}
