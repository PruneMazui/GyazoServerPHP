<?php
/**
 *
 * 
 *
 */

namespace GyazoPhp\StdLib\Logger;

class TaggingLogger extends AbstractLogger
{
    /**
     * ロガー
     *
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * タグ文字列
     *
     * @var string
     */
    private $_tagstr = "";

    /**
     * コンストラクタ
     *
     * @param LoggerInterface $logger
     * @param mixed $tags
     */
    public function __construct(LoggerInterface $logger, $tags)
    {
        $this->_logger = $logger;

        if (!is_array($tags))
        {
            $tags = array($tags);
        }

        if (count($tags))
        {
            array_walk($tags, function (&$val, $key) {

                if (is_int($key))
                {
                    $val = "$val";
                }
                else
                {
                    $val = "$key:$val";
                }
            });

            $this->_tagstr = "<" . implode(", ", $tags) . "> ";
        }
    }

    /**
     * @see \GyazoPhp\StdLib\Logger\LoggerInterface::log()
     */
    public function log($message, $priority, $timestamp)
    {
        $this->_logger->log($this->_tagstr . $message, $priority, $timestamp);
    }
}
