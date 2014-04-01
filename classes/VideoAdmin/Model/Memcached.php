<?php

namespace VideoAdmin\Model;

class Memcached extends \Memcached  {

  public function __construct() {
    parent::__construct();

    new MemcacheInit($this);
  }

}