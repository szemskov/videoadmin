<?php

namespace VideoAdmin\Model\Translation\Ngenix;

class Hds extends \VideoAdmin\Model\Translation\NgenixAbstract {
    

  public function signFile($file) {
    $stream = null;

    $cdn = $this->_cdn;

    if (!$this->_cdnLiveOnly/*&& !$this->_translation->isLive()*/) {
      $cdn = false;
    };

    if ($cdn) {
      $stream = $this->_getCDNStream();
    } else {
      $stream = $this->_getLocalStream();
    }

    $file = $this->_getStreamName($file);    
    
    $queryString = $this->getSignQueryString($file);
    
    if (!$queryString) {
            $stream .= '/' . $file . '/manifest.f4m';
        if ($this->_translation['dvr'] && $this->_translation->isLive())
            $stream .= '?dvr';
        
    } else {
      if ($this->_translation['dvr'] && $this->_translation->isLive()) {
          $queryString .= '&dvr';
      }
      
      $stream .= '/' . $file . '/manifest.f4m?'. $queryString;
    }

    return $stream;
  }

  public function getProvider() {
    return 'video';
  }

  public function getHost() {
    $wowza = new \VideoAdmin\Wowza\Server();
    $ip = $wowza->getServerIp();

    if (!$ip) {
      throw new \ErrorException('Undefined streamer');
    }

    return 'http://' . $ip . ":1935";
  }

  protected function _getLocalStream() {
    $app = null;

    if ($this->_translation->isLive()) {
      if ($this->_translation['dvr']) {
        $app = 'dvredge';
      } else {
        $app = 'edge';
      }
    } else {
      $app = $this->_type;
    }

    $stream = $this->getHost() . '/' . $app;
    return $stream;
  }

  protected function _getCDNStream() {
    $wowza = \Registry::get('WOWZA');
    if (!$this->_translation->isLive())
        return $wowza['hds']['vod'];
    if (!$this->_translation['dvr'])
        return $wowza['hds']['live'];
    return $wowza['hds']['dvr'];
  }

}