<?php
/**
 *
 * 
 *
 */

namespace GyazoPhp\StdLib\Logger;

class FileLogger extends AbstractLogger
{
    /**
     * ファイル名
     *
     * @var string
     */
    private $_fn;

    /**
     * ストリームリソース
     *
     * @var resource
     */
    private $_fp;

    /**
     * ストリームリソースの有効秒数
     *
     * @var resource
     */
    private $_expire;

    /**
     * ストリームリソースの有効期限
     *
     * @var resource
     */
    private $_limit;

    /**
     * @param string $fn
     * @param int $expire
     *
     * @throws \UnexpectedValueException
     */
    public function __construct($fn, $expire = 60)
    {
        $dir = dirname($fn);

        if (!is_dir($dir))
        {
            mkdir($dir, 0777, true);
        }

        if (!is_dir($dir))
        {
            throw new \UnexpectedValueException("Unable create directory \"$dir\".");
        }

        if (is_file($fn))
        {
            if (!is_writable($fn))
            {
                throw new \UnexpectedValueException("Is not writable file \"$fn\".");
            }
        }
        else
        {
            if (!is_writable($dir))
            {
                throw new \UnexpectedValueException("Is not writable dir \"$dir\".");
            }
        }

        $this->_fn = $fn;
        $this->_expire = $expire;
    }

    /**
     * @see \GyazoPhp\StdLib\Logger\LoggerInterface::log()
     */
    public function log($message, $priority, $timestamp)
    {
        $msg = Formatter::format($message, $priority, $timestamp);

        $this->_open();

        try
        {
            if (fwrite($this->_fp, $msg) === false)
            {
                throw new \UnexpectedValueException("Unable to write in \"$this->$_fn\".");
            }

            if ($this->_expire == 0)
            {
                $this->_close();
            }
            else
            {
                fflush($this->_fp);
            }
        }
        catch (\Exception $ex)
        {
            self::_close($this->_fp);
            throw $ex;
        }
    }

    /**
     * ストリームを開く
     *
     * @throws \UnexpectedValueException
     */
    private function _open()
    {
        if (is_resource($this->_fp))
        {
            if (time() > $this->_limit)
            {
                $this->_close();
            }
        }

        if (!is_resource($this->_fp))
        {
            touch($this->_fn);

            $fp = fopen($this->_fn, "a");

            if ($fp == false)
            {
                throw new \UnexpectedValueException("Unable to open file \"$this->_fn\".");
            }

            $this->_fp = $fp;

            if ($this->_expire)
            {
                $this->_limit = time() + $this->_expire;
            }
        }
    }

    /**
     * ストリームを閉じる
     */
    private function _close()
    {
        if (is_resource($this->_fp))
        {
            fclose($this->_fp);
        }

        $this->_fp = null;
    }
}
