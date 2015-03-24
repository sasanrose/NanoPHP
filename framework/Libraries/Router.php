<?php

namespace nanophp\Libraries;

/**
 * Class: Router
 *
 * Router has the responsibility to parse and route requests
 *
 */
class Router
{
    /**
     * _data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * _instance
     *
     * @var object
     */
    protected static $_instance = null;

    /**
     * _params
     *
     * @var array
     */
    protected $_params = array();

    /**
     * _query_strings
     *
     * @var array
     */
    protected $_query_strings = array();

    /**
     * Singleton
     *
     * @return object $_instance Static instance property
     */
    public static function instance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * _parse
     *
     * Parse and fetch all the information from request
     *
     * @param string $request Request to be parsed
     */
    protected function _parse($request = null)
    {
        // First check if there is a custom request sent to the parser
        if (isset($request)) {
            $this->path = $request;
        // Now check if it is web request
        } elseif (PHP_SAPI !== 'cli') {
            // Get HTTP Method
            $this->method = $_SERVER['REQUEST_METHOD'];
            // Set HTTP Protocol
            $this->protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https' : 'http';
            // Set Host, Url and Base Url
            $this->host = $_SERVER['HTTP_HOST'];
            $this->baseUrl = $this->protocol.'://'.$this->host;
            $this->url = $this->protocol.'://'.$this->host.$_SERVER['SCRIPT_NAME'];
            // Find the reqeusted path
            $this->path = str_replace($_SERVER['SCRIPT_NAME'], '', $_SERVER['REQUEST_URI']);

            // Some webservers return the whole request url as the path
            if ($this->path == $_SERVER['REQUEST_URI']) {
                $this->path = '';
            }

            // Set request according to url and path
            $this->request = $this->url.$this->path;

            // Script name may not be absolute
            if (preg_match('/^(.*)\/(.*)$/', $_SERVER['SCRIPT_NAME'], $matches)) {
                $this->baseUrl .= $matches[1];
            }
        // Otherwise it means a command line request
        } elseif (isset($_SERVER['argv'][1])) {
            // We accept the request via arguments
            $this->path = $_SERVER['argv'][1];
        }

        // Try to extract query strings
        if (preg_match('/^(.*)\?(.*)$/', $this->path, $matches)) {
            $this->path = $matches[1];
            foreach (explode('&', $matches[2]) as $match) {
                if (preg_match('/^(.*)=(.*)$/', $match, $strings)) {
                    if ($strings[2]) {
                        $this->_query_strings[$strings[1]] = urldecode($strings[2]);
                    }
                }
            }
        }

        // Extract Params
        $this->_params = explode('/', trim($this->path, '/'));

        // Extract requested controller/command and action
        if (!$this->controller = ucwords(strtolower(array_shift($this->_params)))) {
            $this->controller = ucwords(strtolower(Config::instance()->get('default_controller')));
        }

        if (!$this->action = array_shift($this->_params)) {
            $this->action = Config::instance()->get('default_action');
        }
    }

    /**
     * Route a custom, web or cli request
     *
     * @param string $request
     */
    public function route($request = null)
    {
        // Parse request data
        $this->_parse($request);

        // Create Clas name space
        $class = PHP_SAPI !== 'cli' ? "\\app\\Controllers\\{$this->controller}Controller" : "\\app\\Commands\\{$this->controller}Command";
        // Create action method
        $method = "{$this->action}Action";

        // Create Logging info
        $logInfo = ['params' => $this->_params, 'queris' => $this->_query_strings, 'SAPI' => PHP_SAPI];

        // Check if class and its method do exist and then call them using
        // params
        if (class_exists($class)) {
            $controller = new $class;
            if (method_exists($controller, $method)) {
                call_user_func_array(array($controller, $method), $this->_params);

                \nanophp\Libraries\Application::logger()->info("{$this->controller}/{$this->action}", $logInfo);
                return;
            }
        }

        header("HTTP/1.0 404 Not Found");
        \nanophp\Libraries\Application::logger()->error("{$this->controller}/{$this->action}", $logInfo);
        //TODO: Implement 404 template
    }

    /**
     * Magic getter
     *
     * @param string $key
     * @retrun mixed $this->_data[$key]
     */
    public function __get($key)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    /**
     * Magic setter
     *
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * Fetch value of a certain key from query strings
     *
     * @param string $key
     * @param mixed $default
     */
    public function query($key, $default = null)
    {
        return isset($this->_query_strings[$key]) ? filter_var($this->_query_strings[$key], FILTER_SANITIZE_STRING) : $default;
    }
}
