<?php

namespace nanophp\Library;

/**
 * Class: Config
 *
 * Config class is used to read config file
 *
 * @property object $_instance
 * @property array $_config
 */
class Config
{
    /**
     * _instance
     *
     * @var object
     */
    protected static $_instance = null;

    /**
     * _config
     *
     * @var array
     */
    protected $_config;

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
     * Load config array into $_config property
     *
     * @param array $config Array of the configurations (Possibly read from config file)
     * @return object $this This object in order to be able to chain
     */
    public function load($config)
    {
        $this->_config = $config;

        return $this;
    }

    /**
     * Get a specific key from config file
     *
     * @param string $key A slash sperated string of the key to find in config
     * array
     * @param mixed $defaultValue Return this value in case you do not find the
     * specific key
     *
     * @return mixed $config Return requested configuration
     */
    public function get($key, $defaultValue = false)
    {
        // Explode the key using slash
        $key     = trim($key, '/');
        $indices = explode('/', $key);
        // Store config property in a local variable
        $config  = $this->_config;

        // If indices are empty then return the whole config
        if (empty($indices)) {
            return $config;
        }

        // Walk through indices
        foreach ($indices as $index) {
            if (isset($config[$index])) {
                $config = $config[$index];
            } else {
                $config = $defaultValue;
                break;
            }
        }

        return $config;
    }
}
