<?php

namespace VideoAdmin\Model\Translation\Ngenix;

class Rtsp extends \VideoAdmin\Model\Translation\NgenixAbstract {

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
              $stream = $wowza['rtsp']['dvr'];

            } else {
              $stream = $wowza['rtsp']['live'];
            }
          } else {              
            $stream = $wowza['rtsp']['vod'];
          }
        } else {
          if ($this->_translation->isLive()) {
            $stream = $this->getHost(). '/';
            //$stream = 'rtsp://russiasport-live.cdn.ngenix.net/';
            if ($dvr) {
              $stream .= ($this->_noSign ? 'aT8thoozeedvr' : 'dvredge');
            } else {
              $stream .= ($this->_noSign ? 'aT8thoozee' : 'edge');
            }
          } else {
            //$stream = $wowza['rtsp']['local'] . '/' . $this->_type;
            //$stream = $this->getHost() . '/' . $this->_type;
              $stream = $this->getHost(). '/' . ($this->_noSign ? 'un4Aewo7ut' : 'vod');
          }
        }

        $file = $this->_getStreamName($file);
        $queryString = $this->getSignQueryString($stream);

        $stream .= '/' . $file;

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

      return 'rtsp://' . $ip . ':1935';
    }

}