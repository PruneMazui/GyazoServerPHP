<?php
/**
 *
 * 
 *
 */

namespace GyazoPhp\StdLib\Logger;

class NullLogger extends AbstractLogger
{
    public function log($message, $priority, $timestamp)
    {
        // noop
    }
}
