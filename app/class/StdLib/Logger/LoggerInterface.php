<?php
/**
 *
 * 
 *
 */

namespace GyazoPhp\StdLib\Logger;

interface LoggerInterface
{
    /**
     * @param string $message
     * @param int $priority
     * @param int $timestamp
     */
    public function log($message, $priority, $timestamp);

    /**
     * @param string $message
     */
    public function debug($message);

    /**
     * @param string $message
     */
    public function info($message);

    /**
     * @param string $message
     */
    public function notice($message);

    /**
     * @param string $message
    */
    public function warn($message);

    /**
     * @param string $message
     */
    public function err($message);

    /**
     * @param \Exception $ex
     * @param int $priority
     */
    public function except(\Exception $ex, $priority = LOG_ERR);
}
