<?php

namespace nanophp\Libraries;

/**
 * Class: Template
 *
 * Template factory for NanoPHP
 */
class Template
{
    /**
     * instances
     *
     * @var array
     */
    protected static $instances = array();

    /**
     * factory
     *
     * @param string $driver
     */
    public static function factory($driver = 'php')
    {
        if (!isset(self::$instances[$driver])) {
            $class = "nanophp\\Template\\{$driver}";

            self::$instances[$driver] = new $class;
        }

        return self::$instances[$driver];
    }
}
