<?php
/**
 *
 * 
 *
 */

namespace GyazoHj\StdLib\Logger;

abstract class AbstractLogger implements LoggerInterface
{
    private function _log($message, $priority)
    {
        $timestamp = time();
        $this->log($message, $priority, $timestamp);
    }

    /**
     * @param string $message
     */
    public function debug($message)
    {
        $this->_log($message, LogLevel::DEBUG);
    }

    /**
     * @param string $message
     */
    public function info($message)
    {
        $this->_log($message, LogLevel::INFO);
    }

    /**
     * @param string $message
     */
    public function notice($message)
    {
        $this->_log($message, LogLevel::NOTICE);
    }

    /**
     * @param string $message
     */
    public function warn($message)
    {
        $this->_log($message, LogLevel::WARNING);
    }

    /**
     * @param string $message
     */
    public function err($message)
    {
        $this->_log($message, LogLevel::ERR);
    }

    /**
     * @param Exception $ex
     * @param int $priority
     */
    public function except(\Exception $ex, $priority = LOG_ERR)
    {
        // クラス名から名前空間を削除してメッセージにする
        $message = preg_replace('/^.*\\\\/', "", get_class($ex)) . ': ' . $ex->getMessage();
        $this->_log($message, $priority);
    }
}
