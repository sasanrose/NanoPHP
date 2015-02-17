<?php
final class db
{
    protected static $_instances = array();

    public static function factory($driver = null)
    {
        $driver = isset($driver) ? $driver : App::instance()->config['database']['driver'];

        if (!isset(self::$_instances[$driver])) {
            include_once(App::instance()->drivers.'db/'.(strtolower($driver)).'.php');

            $class = ucwords(strtolower($driver)).'Db';
            self::$_instances[$driver] = new $class;
        }

        return self::$_instances[$driver];
    }
}
