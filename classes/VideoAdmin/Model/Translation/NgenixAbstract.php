<?php

namespace VideoAdmin\Model\Translation;

abstract class NgenixAbstract {

    const PASS = 'Odsnx2we';

    const PREFIX_VOD = 'mp4:russiasport/wowzacontent/';

    const PREFIX_LIVE = 'mp4:';

    protected static $_defaultType = 'vod';

    public static $mobileDevices = false;

    protected $_translation = null;

    protected $_noSign = false;

    protected $_type = null;

    protected $_cdn = true;

    protected $_cdnLiveOnly = false;

    public static function setDefaultType($type) {
      self::$_defaultType = (string)$type;
    }

    public function __construct(\VideoAdmin\Model\Translation\Item $translation) {
        $this->_translation = $translation;

        //deprecated
        if (\Registry::exists('translation_test')) {
            $this->_type = $this->_translation['dvr'] ? 'dvredge' : 'edge';
        } else {
            $this->_type = self::$_defaultType;
        }

        //конфиг вовзы
        $wowza = \Registry::get('WOWZA');

        //использовать ли cdn
        if (isset($wowza['cdn'])) {
          $this->_cdn = $wowza['cdn'];
        }

        if (false == $this->_cdn && $translation['cdn']) {
          $this->_cdn = (bool)$translation['cdn'];
          $this->_cdnLiveOnly = true;
        }

        //использовать ли подпись
        if (isset($wowza['sign'])) {
          $this->_noSign = !$wowza['sign'];
        }

        //@todo сейчас подписываются только live потоки с CDN NGENIX
        /*if (false == $this->_noSign) {

          if ($this->_cdnLiveOnly && !$translation->isLive()) {
            $this->_noSign = true;
          }

          if (!$this->_cdn) {
            $this->_noSign = true;
          }

        }*/

        if (!isset($_GET['force_sign'])) {
          $ip = \Network::getClientIp();

          if (\Network::isPrivateNetwork($ip))
            $this->_noSign = true;
        }
    }

    /**
     * @param $file
     * @return mixed
     */
    abstract public function signFile($file);

    /**
     * Возвращает подпись на основе имени потока, текущем времени и ip-адресе
     *
     * @param $stream
     * @param $time
     * @param $ip
     * @return bool|string
     */
    public function getSign($stream, &$time, $ip) {

      if ($this->_noSign) {
        return false;
      }

      $sign = null;

      while(true) {
        $sign = base64_encode(md5(self::PASS . $stream . $time . $ip, true));

        if (!preg_match('/[\/\+]+/', $sign)) {
          break;
        }

        $time += 1;
      }

      /*echo 'file: ' . $stream, '<br>';
      echo 'time: ' . $time, '<br>';
      echo 'ip: ' . $ip, '<br>';
      echo 'подпись: ' . $sign, '<br>';
      exit;*/

      return $sign;
    }

	public function generateSign($url, $timestamp, $ip) {
		$country_code = $this->getCountryCode($ip);
		$token = substr(md5(time()), 0, 10);
		$data = $url . $timestamp . $token . "0:$country_code";
		$mysignature = $this->_hmacsha1(self::PASS, $data);
		$decoded_query = "timestamp=" . $timestamp . "&token=" . $token . "&signature=" . $mysignature;
		$encoded_query = base64_encode($decoded_query);
		
		return $encoded_query;
	}

    public function getCountryCode($remote_addr = '') {
        return \Network::getCountryCode($remote_addr);
    }

    protected function _hmacsha1($key, $data) {
        $blocksize = 64;
        $hashfunc = 'sha1';
        if (strlen($key) > $blocksize)
            $key = pack('H*', $hashfunc($key));
        $key = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);
        $hmac = pack(
                'H*', $hashfunc(
                        ($key ^ $opad) . pack(
                                'H*', $hashfunc(
                                        ($key ^ $ipad) . $data
                                )
                        )
                )
        );
        return bin2hex($hmac);
    }

    public function getSignQueryString($file) {
      $time = time();
      $sign = $this->getSign($file, $time, \Vdl::getClientIpForSign());
      $queryString = '';

      if ($sign) {

        //кодируем подпись для локальной вовзы
        /*if (!$this->_checkCDN()) {
          $queryString = urlencode("t=" . $time . "&h=" . $sign);
        } else {
          $queryString = "t=" . $time . "&h=" . $sign;
        }*/

        $queryString = "t=" . $time . "&h=" . $sign;

      }

      return $queryString;
    }

    public function setCDN($flag) {
      $this->_cdn = (bool)$flag;
      return $this;
    }

    public function setNoSign($flag) {
      $this->_noSign = (bool)$flag;
      return $this;
    }

    protected function _checkCDN() {
    
        $cdn = $this->_cdn;

        if (!$this->_cdnLiveOnly/* && !$this->_translation->isLive()*/) {
          $cdn = false;
        };
        return $cdn;
    }

    protected function _getStreamName($file) {
      $cdn = $this->_checkCDN();
        

      if ($cdn) {
        if ($this->_translation->isLive()) {
        $file = self::PREFIX_LIVE . $file;
        } else {
        $file = self::PREFIX_VOD . $file;
        }
      }
      /*elseif ($this->_translation->isArchive()) {
        $file = 'mp4:' . $file;
      }*/
      else {
        $file = 'mp4:' . $file;
      }

      return $file;
    }

}