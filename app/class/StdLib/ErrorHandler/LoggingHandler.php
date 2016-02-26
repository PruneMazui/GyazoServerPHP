<?php
/**
 * エラーハンドラ PHPエラーをロギング
 *
 * @package   StdLib
 * 
 */

namespace GyazoPhp\StdLib\ErrorHandler;

/**
 * エラーハンドラ PHPエラーをロギング
 *
 * @package   StdLib
 * 
 */
class LoggingHandler extends \GyazoPhp\StdLib\ObjectAbstract
{
    private $_original = false;

    /**
     * エラーハンドラの登録
     */
    public static function register()
    {
        static $obj;

        if (isset($obj) === false)
        {
            $obj = new self;
        }
    }

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->_original = set_error_handler(array(__CLASS__, '_handler'));
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

    /**
     * エラーを表示するストリームを得る
     *
     * @return resource|null|true ストリームリソース、true なら php://output、null なら 出力なし
     */
    public static function getDisplayErrorsStream()
    {
        $display_errors = ini_get('display_errors');

        if ($display_errors)
        {
            if (strtolower($display_errors) === 'stderr')
            {
                if (defined('STDERR') && is_resource(STDERR))
                {
                    return STDERR;
                }
            }

            return true;
        }

        return null;
    }

    /**
     * 例外をログる
     *
     * 握りつぶした例外をログるために使用する
     *
     * @param Exception $ex
     */
    public static function logException(\Exception $ex)
    {
        $log_errors = ini_get('log_errors');
        $display_stream = self::getDisplayErrorsStream();

        if ($log_errors || $display_stream)
        {
            $message = "Exception: " . (string)$ex;

            if ($log_errors)
            {
                error_log("PHP " . $message);
            }

            if (is_resource($display_stream))
            {
                fputs($display_stream, $message . PHP_EOL);
            }
            else if ($display_stream)
            {
                echo $message . PHP_EOL;
            }
        }
    }

    /**
     * エラーハンドラ
     */
    public static function _handler($errno, $errstr, $errfile, $errline)
    {
        // @付きの場合は 0 になるので無視する
        if (error_reporting() == 0)
        {
            // 標準の処理を呼び出し
            return false;
        }

        // 発生する可能性のあるものだけ
        static $map = array (
            E_RECOVERABLE_ERROR => 'Error',

            E_WARNING           => 'Warning',
            E_USER_WARNING      => 'Warning',

            E_NOTICE            => 'Notice',
            E_USER_NOTICE       => 'Notice',

            E_DEPRECATED        => 'Deprecated',
            E_USER_DEPRECATED   => 'Deprecated',

            E_STRICT            => 'Strict',
        );

        $errtag = '';

        if (isset($map[$errno]))
        {
            $errtag = $map[$errno];
        }
        else
        {
            // @codeCoverageIgnoreStart
            $errtag = "Unknown($errno)";
            // @codeCoverageIgnoreEnd
        }

        // エラー関連設定を取得
        $log_errors = ini_get('log_errors');
        $display_stream = self::getDisplayErrorsStream();

        if ($log_errors || $display_stream)
        {
            // エラーメッセージ
            $message = sprintf('%s:  %s in %s on line %s', $errtag, $errstr, $errfile, $errline);

            // スタックトレース
            $trace = null;

            foreach (debug_backtrace(false) as $i => $f)
            {
                if ($trace !== null)
                {
                    $trace[] = $f;
                }
                else if(isset($f['class']) && ($f['class'] === __CLASS__))
                {
                    // 自分自身を除外
                }
                else
                {
                    $trace = array();
                }
            }

            // @codeCoverageIgnoreStart
            if ($trace === null)
            {
                $trace = array();
            }
            // @codeCoverageIgnoreEnd

            $i = 0;

            foreach ($trace as $f)
            {
                $f = $trace[$i];

                $str = sprintf('#%d %s(%s): %s%s%s()',
                    $i++,
                    isset($f['file'])     ? $f['file']     : null,
                    isset($f['line'])     ? $f['line']     : null,
                    isset($f['class'])    ? $f['class']    : null,
                    isset($f['type'])     ? $f['type']     : null,
                    isset($f['function']) ? $f['function'] : null
                );

                $message .= PHP_EOL . $str;
            }

            if ($log_errors)
            {
                // エラーログ
                error_log("PHP " . $message);
            }

            if (is_resource($display_stream))
            {
                fputs($display_stream, $message . PHP_EOL);
            }
            else if ($display_stream)
            {
                echo $message . PHP_EOL;
            }

            // 標準の処理をスキップする
            return true;
        }
    }
}
