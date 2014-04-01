<?php

namespace VideoAdmin\Model\Translation;

abstract class PlayerAbstract implements \VideoAdmin\Decorator\DecoratorInterface {

    const HD_360P = 1;

    const HD_720p = 2;

    protected $_translation = null;

    protected $_quality = null;

    protected $_template = null;

    protected $_image = null;

    protected $_width = 960;

    protected $_height = 560;

    protected $_hls = null;
    protected $_hds = null;
    protected $_rtsp = null;
    protected $_rtmp = null;

    public function __construct(\VideoAdmin\Model\Translation\Item $translation) {
        $this->_translation = $translation;
        $this->_quality = self::HD_360P;

        if (isset($_GET['w']) && $_GET['w'] > 0) {
            $this->_width = (int)$_GET['w'];
        }

        if (isset($_GET['h']) && $_GET['h'] > 0) {
            $this->_height = (int)$_GET['h'];
        }

        if (isset($_GET['t'])) {
            $t = $_GET['t'];
            // Формат: 2h9m37s
            if (preg_match('/^\s*((\d+)h)?((\d+)m)?((\d+)s)?\s*$/', $t, $m) && count($m) > 1) {
                $t = 0;
                if (!empty($m[2])) { // h
                    $t += $m[2] * 3600;
                }
                if (!empty($m[4])) { // m
                    $t += $m[4] * 60;
                }
                if (!empty($m[6])) { // s
                    $t += $m[6];
                }
            }
            $this->_start = (int)$t;
        } else {
            $this->_start = 0;
        }

        $this->_initNgenix($translation);
    }

    protected function _initNgenix(\VideoAdmin\Model\Translation\Item $translation) {
      $this->_hls = new Ngenix\Hls($translation);
      $this->_hds = new Ngenix\Hds($translation);
      $this->_rtsp = new Ngenix\Rtsp($translation);
      $this->_rtmp = new Ngenix\Rtmp($translation);
    }

    public function setTemplate($t) {
        $this->_template = $t;
        return $this;
    }

    public function setImage($image) {
        $this->_image = $image;
        return $this;
    }

    abstract public function getRawFile($quality = null);

    public function setQuality($quality) {
        $this->_quality = (int)$quality;
        return $this;
    }

    public function getQuality() {
        return $this->_quality;
    }

    public function getTranslation() {
        return $this->_translation;
    }

    public function setWidth($w) {
        $this->_width = (int)$w;
        return $this;
    }

    public function setHeight($h) {
        $this->_height = (int)$h;
        return $this;
    }

    public function disableSign() {
        $this->_hls->setNoSign(true);
        $this->_rtsp->setNoSign(true);
        $this->_hds->setNoSign(true);
        $this->_rtmp->setNoSign(true);
        return $this;
    }

    public function disableCDN() {
        $this->_hls->setCDN(false);
        $this->_rtsp->setCDN(false);
        $this->_rtmp->setCDN(false);
        return $this;
    }

    public function decorate() {
      return null;
    }

}
