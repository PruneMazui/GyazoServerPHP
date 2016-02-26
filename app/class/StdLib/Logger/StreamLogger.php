<?php
/**
 *
 * 
 *
 */

namespace GyazoPhp\StdLib\Logger;

class StreamLogger extends AbstractLogger
{
    /**
     * @var resource
     */
    private $_stream;

    /**
     * @param resource $stream
     */
    public function __construct($stream)
    {
        $this->_stream = $stream;
    }

    /**
     * @see \GyazoPhp\StdLib\Logger\Handler\LoggerInterface::log()
     */
    public function log($message, $priority, $timestamp)
    {
        $msg = Formatter::format($message, $priority, $timestamp);

        fputs($this->_stream, $msg);
    }
}
