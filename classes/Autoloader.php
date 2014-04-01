<?php

class Autoloader {

    const DEFAULT_PATH = '/classes';

    const DEFAULT_EXT = 'php';

    protected $_path = null;

    private $_registered = null;

    public function __construct($path = null) {
        if (null === $path) {
            if (defined('ROOT_DIR')) {
              $path = ROOT_DIR . self::DEFAULT_PATH;
            } else {
              $path = getcwd() . self::DEFAULT_PATH;
            }
        }
        $this->_path = (string)$path;
        $this->registerLoader();
    }

    public function registerLoader() {
        if (!$this->_registered) {
            spl_autoload_register(array($this, 'loadClass'));
            $this->_registered = true;
        }
    }

    public function unregisterLoader() {
        if ($this->_registered) {
            spl_autoload_unregister(array($this, 'loadClass'));
            $this->_registered = false;
        }
    }

    public function loadClass($class) {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
        $path = $this->_path . DIRECTORY_SEPARATOR . $path . '.' . self::DEFAULT_EXT;
        include_once $path;
    }

    public function call($class, $method, $args = null) {
        if (!class_exists($class)) {
            throw new ErrorException("Class $class not found");
        }

        $object = new $class;

        if (!method_exists($object, $method)) {
            throw new ErrorException("Method $method not found");
        }

        return call_user_func_array(array($object, $method), (array)$args);
    }

}