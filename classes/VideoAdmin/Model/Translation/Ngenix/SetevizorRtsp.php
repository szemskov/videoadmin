<?php

namespace VideoAdmin\Model\Translation\Ngenix;

class SetevizorRtsp extends Rtsp {

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

    $queryString = $this->getSignQueryString($stream);

    $stream .= '/' . $file;

    if ($queryString) {
      $stream .= '?' . $queryString;
    }

    return $stream;

  }

}