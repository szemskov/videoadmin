<?php

class Template {

    private $_params = array();

    private $_template = null;

    private $_path = './views';

    public function __construct($template, array $params = array()) {
        $this->_template = $this->_path . DIRECTORY_SEPARATOR . $template . '.tpl.php';
        $this->_params = $params;
    }

    public static function show($template, array $params = array(), $clean = false) {
        $object = new self($template, $params);

        ob_start();
        $object->view();

        if (false == $clean) {
          return ob_get_clean();
        } else {
          return trim(preg_replace("/[\r\n]/", '', ob_get_clean()));
        }
    }

    public function view() {
        $file = $this->_template;
        if (file_exists($file)) {
            include $file;
        }
    }

    public function escape($data) {
        return htmlspecialchars((string)$data);
    }

    public function __set($key, $value) {
        throw new LogicException('You cannot modify template param');
    }

    public function __get($key) {
        return isset($this->_params[$key]) ? $this->_params[$key] : null;
    }

}