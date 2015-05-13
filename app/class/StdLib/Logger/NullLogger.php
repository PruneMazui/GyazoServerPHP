<?php
/**
 *
 * 
 *
 */

namespace GyazoHj\StdLib\Logger;

class NullLogger extends AbstractLogger
{
    public function log($message, $priority, $timestamp)
    {
        // noop
    }
}
