<?php

namespace VideoAdmin\Model;

class MemcacheInit {

  private static $_servers = array(
    '127.0.0.1:11211'
  );

  private $_memcache = null;

  public static function setServers(array $servers) {
    self::$_servers = $servers;
  }

  public function __construct($memcache) {
    $this->_memcache = $memcache;
    $this->_init();
  }

  protected function _init() {
    foreach (self::$_servers as $server) {
      list($host, $port) = explode(':', $server);
      $this->_memcache->addServer($host, $port);
    }
  }

}