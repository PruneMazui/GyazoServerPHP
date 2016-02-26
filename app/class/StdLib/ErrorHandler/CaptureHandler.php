<?php
/**
 * エラーハンドラ
 *
 * @package   StdLib
 * 
 */

namespace GyazoPhp\StdLib\ErrorHandler;

/**
 * エラーハンドラ
 *
 * @package   StdLib
 * 
 */
class CaptureHandler extends \GyazoPhp\StdLib\ObjectAbstract
{
    private $_obj;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->_obj = new CaptureHandlerInternal();
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
        $this->_obj->restore();
    }

    /**
     * エラーの一覧を取得する
     *
     * @return array ErrorException の配列
     */
    public function getErrors()
    {
        return $this->_obj->getErrors();
    }

    /**
     * 最初のエラーを例外として投げる
     *
     * 最初のエラーを例外として投げる。
     * エラーが一回も発生していない場合も例外を投げます。
     *
     * @param string $exceptionClass 投げる例外クラス
     */
    public function throwFirst($exceptionClass = null)
    {
        $errors = $this->_obj->getErrors();

        /* @var $err PHPError */
        foreach ($errors as $err)
        {
            $ex = new \ErrorException($err->getMessage(), 0, $err->getSeverity(), $err->getFile(), $err->getLine());

            if ($exceptionClass === null)
            {
                throw $ex;
            }
            else
            {
                throw new $exceptionClass($err->getMessage(), 0, $ex);
            }
        }

        if ($exceptionClass === null)
        {
            throw new \ErrorException("unknown error");
        }
        else
        {
            throw new $exceptionClass("unknown error");
        }
    }
}

class PHPError
{
    private $_errno;
    private $_errstr;
    private $_errfile;
    private $_errline;

    public function __construct($errno, $errstr, $errfile, $errline)
    {
        $this->_errno = $errno;
        $this->_errstr = $errstr;
        $this->_errfile = $errfile;
        $this->_errline = $errline;
    }

    public function getSeverity()
    {
        return $this->_errno;
    }

    public function getMessage()
    {
        return $this->_errstr;
    }

    public function getFile()
    {
        return $this->_errfile;
    }

    public function getLine()
    {
        return $this->_errline;
    }
}

class CaptureHandlerInternal
{
    private $_original = false;
    private $_errors = array();

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->_original = set_error_handler(array($this, '_handler'));
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

    public function _handler($errno, $errstr, $errfile, $errline)
    {
        $this->_errors[] = new PHPError($errno, $errstr, $errfile, $errline);
    }

    /**
     * エラーの一覧を取得する
     *
     * @return array CaptureHandlerInternal の配列
     */
    public function getErrors()
    {
        return $this->_errors;
    }
}
