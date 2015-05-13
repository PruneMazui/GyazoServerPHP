<?php
/**
 * エラーハンドラ PHPエラーを例外に変換
 *
 * @package   StdLib
 * 
 */

namespace GyazoHj\StdLib\ErrorHandler;

/**
 * エラーハンドラ PHPエラーを例外に変換
 *
 * @package   StdLib
 * 
 */
class ThrownHandler extends \GyazoHj\StdLib\ObjectAbstract
{
    private $_original = false;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->_original = set_error_handler(self::_handler());
    }

    /**
     * デストラクタ
     */
    public function __destruct()
    {
        $this->restore();
    }

    /**
     * エラーハンドラを元に戻す
     */
    public function restore()
    {
        if ($this->_original !== false)
        {
            restore_error_handler();
            $this->_original = false;
        }
    }

    private static function _handler()
    {
        return function($errno, $errstr, $errfile, $errline) {
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        };
    }
}
