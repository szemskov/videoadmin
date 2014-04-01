<?php

namespace VideoAdmin\Model\Translation\Ngenix;

class Rtmp extends \VideoAdmin\Model\Translation\NgenixAbstract {

    public function signFile($file) {
        $stream = $this->_getStreamName($file);

        $queryString = $this->getSignQueryString($stream);

        if ($queryString) {
          $stream .= '?' . $queryString;
        }

        return $stream;
    }

    public function getProvider() {
        return 'rtmp';
    }

    public function getStreamer() {
        $wowza = \Registry::get('WOWZA');

        $stream = null;

        $cdn = $this->_cdn;

        if (!$this->_cdnLiveOnly/* && !$this->_translation->isLive()*/) {
          $cdn = false;
        };

        if ($cdn) {
          if ($this->_translation->isLive()) {
            $stream = $wowza['rtmp']['live'];
          } else {
            $stream = $wowza['rtmp']['vod'];
          }
        } else {
          if ($this->_translation->isLive() || $this->_translation->isAnnounce()) {
            //fix
            //$stream = $this->getHost() . '/edge';
            $stream = $this->getHost() . '/' . ($this->_type == 'dvredge' ? $this->_type : 'edge');
          } else {
            //$stream = $wowza['rtmp']['local'] . '/' . $this->_type;
            if ($this->_type == 'vod')
              $stream = $this->getHost(). '/' . ($this->_noSign ? 'un4Aewo7ut' : 'vod');
            else 
                $stream = $this->getHost(). '/' . ($this->_type == 'dvredge' ? $this->_type : 'edge');
          }
        }

        return $stream;
    }

    public function getHost() {
      $wowza = new \VideoAdmin\Wowza\Server();
      $ip = $wowza->getServerIp();

      if (!$ip) {
        throw new \ErrorException('Undefined streamer');
      }

      return 'rtmp://' . $ip;
    }

}