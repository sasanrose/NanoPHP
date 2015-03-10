<?php

namespace nanophp\Library;

use nanophp\Library\Config;
use nanophp\Library\Router;

/**
 * Class: Application
 *
 * Is the basic class to run a NanoPHP application
 *
 */
class Application
{
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
        // Set default timezone if it is set in config file
        if ($timezone = Config::instance()->get('/timezone')) {
            date_default_timezone_set($timezone);
        }

        // Do the actual job and route
        Router::instance()->route();
    }
}
