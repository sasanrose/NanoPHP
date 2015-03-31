<?php

namespace nanophp\Libraries;

use nanophp\Libraries\Config;
use nanophp\Libraries\Router;

use Monolog\Logger;

/**
 * Class: Application
 *
 * Is the basic class to run a NanoPHP application
 *
 */
class Application
{
    /**
     * _logger
     *
     * @var object
     */
    protected static $_logger = null;

    /**
     * __construct
     *
     * @param array $config Main config
     */
    public function __construct($config)
    {
        Config::instance()->load($config);
    }

    /**
     * We start Application from here
     */
    public function run()
    {
        // Check if log directory exists and is writable
        $logDir = \nanophp\Libraries\Config::instance()->get('log/dir');

        if (!is_dir($logDir)) {
            die("{$logDir} is not a directory");
        } elseif (!is_writable($logDir)) {
            die("{$logDir} is not writable");
        }

        // Set Error Handler
        (new \nanophp\Libraries\Error)->init();

        // Set default timezone if it is set in config file
        if ($timezone = Config::instance()->get('/timezone')) {
            date_default_timezone_set($timezone);
        }

        // Do the actual job and route
        Router::instance()->route();
    }

    /**
     * logger
     *
     * Get instance of logger
     */
    public static function logger()
    {
        if (!isset(self::$_logger)) {
            self::$_logger = new Logger('NanoPHP');
            self::$_logger->pushHandler(\nanophp\Libraries\Config::instance()->get('log/handler'));
        }

        return self::$_logger;
    }
}
