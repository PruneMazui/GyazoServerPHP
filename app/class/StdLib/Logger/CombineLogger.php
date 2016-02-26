<?php
/**
 *
 * 
 *
 */

namespace GyazoPhp\StdLib\Logger;

class CombineLogger extends AbstractLogger
{
    /**
     * @var array
     */
    private $_loggers = array();

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        if ($logger)
        {
            $this->addLogger($logger);
        }
    }

    /**
     * @param LoggerInterface $logger
     */
    public function addLogger(LoggerInterface $logger)
    {
        $this->_loggers[] = $logger;
        return $this;
    }

    /**
     * @see \GyazoPhp\StdLib\Logger\LoggerInterface::log()
     */
    public function log($message, $priority, $timestamp)
    {
        foreach ($this->_loggers as $logger)
        {
            $logger->log($message, $priority, $timestamp);
        }
    }
}
