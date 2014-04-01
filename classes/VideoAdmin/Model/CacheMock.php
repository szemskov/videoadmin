<?php

namespace VideoAdmin\Model;

class CacheMock {

  public function __call($method, $args) {
    return false;
  }

}