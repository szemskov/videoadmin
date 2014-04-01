<?php

namespace VideoAdmin\Model;

class Memcache extends \Memcache  {

  public function __construct() {
    new MemcacheInit($this);
  }

  public function add($key, $value, $expire = 0) {
    return parent::add($key, $value, null, $expire);
  }

  public function set($key, $value, $expire = 0) {
    return parent::set($key, $value, null, $expire);
  }

}