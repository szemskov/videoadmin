<?php

class Registry {

    private static $_instance = null;

    private $_data = array();

    private function __construct() {}

    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function set($key, $value) {
        self::getInstance()->{$key} = $value;
    }

    public static function get($key) {
        return self::getInstance()->{$key};
    }

    public static function exists($key) {
        return self::getInstance()->isRegistered($key);
    }

    public function __set($key, $value) {
        $this->_data[$key] = $value;
    }

    public function __get($key) {
        return isset($this->_data[$key]) ? $this->_data[$key] : null;
    }

    public function isRegistered($key) {
        return array_key_exists($key, $this->_data);
    }

}