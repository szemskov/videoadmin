<?php

namespace VideoAdmin\Model\Translation\Ngenix;

class SetevizorRtmp extends Rtmp {

  public function signFile($file) {
    return $file;
  }

  public function getStreamer() {
    return $this->getHost();
  }

  public function getHost() {
    return 'rtmp://127.0.0.1:3888';
  }

}