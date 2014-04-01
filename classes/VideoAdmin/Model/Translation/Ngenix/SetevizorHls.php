<?php

namespace VideoAdmin\Model\Translation\Ngenix;

class SetevizorHls extends Hls {

  public function __construct(\VideoAdmin\Model\Translation\Item $translation) {
    parent::__construct($translation);

    //@todo !prod!
    //$this->setNoSign(false);
  }

  public function signFile($file) {
    $file = 'mp4:' . $file;
    $stream = null;

    if ($this->_translation->isLive()) {
      $stream = $this->getHost(). '/edge';
    } else {
      $stream = $this->getHost(). '/vod';
    }

    $queryString = $this->getSignQueryString($file);

    $stream .= '/' . $file . '/playlist.m3u8';

    if ($queryString) {
      $stream .= '?' . $queryString;
    }

    return $stream;

  }

}