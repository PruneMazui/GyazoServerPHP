<?php
/**
 * LogLevel constant
 *
 * [RFC 5424 - The Syslog Protocol](http://tools.ietf.org/html/rfc5424 "RFC 5424 - The Syslog Protocol")
 *
 * 
 *
 */

namespace GyazoHj\StdLib\Logger;

class LogLevel
{
    const EMERG   = 0;
    const ALERT   = 1;
    const CRIT    = 2;
    const ERR     = 3;
    const WARNING = 4;
    const NOTICE  = 5;
    const INFO    = 6;
    const DEBUG   = 7;

    private static $_map = array(
        self::EMERG   => 'EMERG  ',
        self::ALERT   => 'ALERT  ',
        self::CRIT    => 'CRIT   ',
        self::ERR     => 'ERROR  ',
        self::WARNING => 'WARNING',
        self::NOTICE  => 'NOTICE ',
        self::INFO    => 'INFO   ',
        self::DEBUG   => 'DEBUG  ',
    );

    public static function toString($level)
    {
        if (isset(self::$_map[$level]))
        {
            return self::$_map[$level];
        }
        else
        {
            return "UNK($level)";
        }
    }
}
