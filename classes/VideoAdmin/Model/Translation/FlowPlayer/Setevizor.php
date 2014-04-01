<?php

namespace VideoAdmin\Model\Translation\FlowPlayer;

class Setevizor extends \VideoAdmin\Model\Translation\FlowPlayer\PlayerAbstract implements \VideoAdmin\Decorator\DecoratorInterface {

  protected function _initNgenix(\VideoAdmin\Model\Translation\Item $translation) {
    $this->_hls = new \VideoAdmin\Model\Translation\Ngenix\SetevizorHls($translation);
    $this->_hds = null;
    $this->_rtsp = new \VideoAdmin\Model\Translation\Ngenix\SetevizorRtsp($translation);
    $this->_rtmp = new \VideoAdmin\Model\Translation\Ngenix\SetevizorRtmp($translation);
  }

  public function getRawFile($quality = null, $directLine = true) {
    $file = null;

    if (null === $quality) {
      $quality = $this->getQuality();
    }

    if (!$directLine) {
      $file =  $this->_translation->getPublishPoint();

    } else {
      $file = $this->_translation->getLine();
    }

    return $file . '_' . $quality;
  }

  public function decorate() {
    $translation = $this->getTranslation();

    $file360pLine = $this->getRawFile(self::HD_360P);
    $file720pLine  = $this->getRawFile(self::HD_720p);

    $file360p = $this->getRawFile(self::HD_360P, false);
    $file720p = $this->getRawFile(self::HD_720p, false);

    $hdsFile = null;
    $hdsFile2 = null;
    $hlsFile = $this->_hls->signFile($file360p);
    $rtspFile = $this->_rtsp->signFile($file360p);
    $rtmpFile360p = $this->_rtmp->signFile($file360pLine);
    $rtmpFile720p = $this->_rtmp->signFile($file720pLine);

    $provider = $this->_rtmp->getProvider();
    $streamer = $this->_rtmp->getStreamer();

    $params = array(
      'hls_file'      => $hlsFile,
      'rtsp_file'     => $rtspFile,
      'rtmp_file'     => $rtmpFile360p,
      'rtmp_file2'    => $rtmpFile720p,
      'hds_file'      => $hdsFile,
      'hds_file2'     => $hdsFile2,
      'width'         => $this->_width,
      'height'        => $this->_height,
      'translation_id'  => $translation->getId(),
      'title'         => $translation['name'],
      'provider'      => $provider,
      'streamer'      => $streamer,
      'image'         => $this->_image,
      'announce'      => $translation->isAnnounce(),
      'start_time'    => date('Y-m-d H:i', $translation['date_start']),
      'setevizor'     => true
    );

    return \Template::show(
      $this->_template,
      $params
    );
  }

  public function __toString() {
    return $this->decorate();
  }

}