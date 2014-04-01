<?php

namespace VideoAdmin\Model\Translation\Ngenix;

class Hls extends \VideoAdmin\Model\Translation\NgenixAbstract {

	public function __construct(\VideoAdmin\Model\Translation\Item $translation) {
		parent::__construct($translation);

		//@todo !prod!
		$this->setNoSign(true);
	}

    public function signFile($file) {
        $wowza = \Registry::get('WOWZA');

        $stream = null;

		    //@todo !prod!
        $cdn = $this->_cdn;
        //$cdn = self::$mobileDevices;

        if (!$this->_cdnLiveOnly/* && !$this->_translation->isLive()*/) {
        $cdn = false;
        };
        
        
        $dvr = $this->_translation['dvr'];

        if ($cdn) {

            if ($this->_translation->isLive()) {
              if ($dvr) {
                $stream = $wowza['hls']['dvr'];

              } else {
                $stream = $wowza['hls']['live'];
              }

            } else {
              $stream = $wowza['hls']['vod'];
            }
        } else {
          if ($this->_translation->isLive()) {
            $stream = $this->getHost(). '/';
            //$stream = 'http://russiasport-live.cdn.ngenix.net/';
            if ($dvr) {
              $stream .= ($this->_noSign ? 'aT8thoozeedvr' : 'dvredge');
            } else {
              $stream .= ($this->_noSign ? 'aT8thoozee' : 'edge');
            }
          } else {
            //$stream = $wowza['rtmp']['local'] . '/' . $this->_type;
            if ($this->_type == 'vod'){
              $stream = $this->getHost(). '/' . ($this->_noSign ? 'un4Aewo7ut' : 'vod');
            }
            else {
                $stream = $this->getHost(). '/' . ($dvr ? $this->_type : 'edge');
                if ($this->_noSign)
                    $stream = $this->getHost(). '/' . ($dvr ? 'aT8thoozeedvr' : 'aT8thoozee');                
            }
          }
        }

        $file = $this->_getStreamName($file);

        $queryString = $this->getSignQueryString($file);

        $stream .= '/' . $file . '/playlist.m3u8';

        if ($queryString) {
          $stream .= '?' . $queryString;
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

        return 'http://' . $ip;
    }

}