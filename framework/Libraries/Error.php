<?php

namespace nanophp\Libraries;

use nanophp\Libraries\Config;
use Monolog\Logger;

/**
 * Class: Error
 *
 * Error handler class of NanoPHP
 *
 * @final
 */
final class Error
{
    /**
     * init
     *
     * Register error handling functions and set display errors
     */
    public function init()
    {
        // Check if we should display errors
        if (Config::instance()->get('production')) {
            ini_set('display_errors', 0);
            ini_set('display_startup_errors', 0);
        }

        // Set error reporting if there is a reporting level set in config file
        if (($error_reporting = Config::instance()->get('error/reporting', false)) !== False) {
            error_reporting($error_reporting);
        }

        // Set a custom error handler
        set_error_handler([$this, 'errorHandler']);

        // Set a custom exception handler
        set_exception_handler([$this, 'exceptionHanler']);

        // Set a custom shutdown handler
        register_shutdown_function([$this, 'shutdownHandler']);
    }

    /**
     * shutdownHandler
     *
     * Custom shutdown handler
     */
    public function shutdownHandler()
    {
        // Check what caused the application to shutdown
        $error = error_get_last();

        if ($error) {
            // It seems and error occured
            // Get error type
            $type = $this->_getError($error['type']);

            // Log error
            \nanophp\Libraries\Application::logger()->log($type, "{$error['message']} on {$error['file']}:{$error['line']}");
        }
    }

    /**
     * exceptionHanler
     *
     * Custom exception handler
     *
     * @param Exception $e
     */
    public function exceptionHanler(Exception $e)
    {
        // Log exception
        \nanophp\Libraries\Application::logger()->log(Logger::ERROR, "{$e->getMessage()} on {$e->getFile()}:{$e->getLine()}");
    }

    /**
     * errorHandler
     *
     * Custom error handler function
     *
     * @param int $no
     * @param string $str
     * @param string $file
     * @param int $line
     * @param string $context
     */
    public function errorHandler($no, $str, $file, $line, $context)
    {
        // Check if error is included in error reporting
        if (!(error_reporting() & $no)) {
            return;
        }

        // Get error type
        $type = $this->_getError($no);

        // Log error
        \nanophp\Libraries\Application::logger()->log($type, "{$str} on {$file}:{$line}");
    }

    /**
     * _getError
     *
     * Return Error type
     *
     * @param int $type
     */
    protected function _getError($type)
    {
        switch ($type) {
            case E_WARNING: // 2 //
                return Logger::WARNING;
            case E_NOTICE: // 8 //
                return Logger::NOTICE;
            case E_CORE_WARNING: // 32 //
                return Logger::WARNING;
            case E_USER_WARNING: // 512 //
                return Logger::WARNING;
            case E_USER_NOTICE: // 1024 //
                return Logger::NOTICE;
            case E_DEPRECATED: // 8192 //
                return Logger::WARNING;
            case E_USER_DEPRECATED: // 16384 //
                return Logger::WARNING;
        }

        return Logger::ERROR;
    }
}
