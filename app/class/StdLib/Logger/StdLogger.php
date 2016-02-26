<?php
/**
 *
 * 
 *
 */

namespace GyazoPhp\StdLib\Logger;

class StdLogger extends CombineLogger
{
    private static $_defaultConsole = true;

    public static function setDefaultConsole($console)
    {
        self::$_defaultConsole = $console;
    }

    public function __construct($fn)
    {
        if (self::$_defaultConsole === true)
        {
            self::$_defaultConsole = null;

            if (defined('STDOUT') && is_resource(STDOUT))
            {
                if (function_exists('posix_isatty') && posix_isatty(STDOUT))
                {
                    self::$_defaultConsole = STDOUT;
                }
            }
        }

        if (self::$_defaultConsole)
        {
            $this->addLogger(new StreamLogger(self::$_defaultConsole));
        }

        $this->addLogger(new FileLogger($fn));
    }
}
