<?php

namespace VideoAdmin\Model\Translation\JwPlayer;

abstract class PlayerAbstract extends \VideoAdmin\Model\Translation\PlayerAbstract {

  const TEMPLATE = 'player_jwplayer_5_10_iframe_rtmp_spbsigncheck';

  protected $_sharedLinks = false;

  protected $_template = 'player_jwplayer_5_10_iframe_rtmp_spbsigncheck';

  abstract public function getStretching();

  public function getAutoStart() {
    return false;
  }

  public function getStart() {
    return !empty($this->_start) ? $this->_start : false;
  }

  public function getDuration() {
    return false;
  }

  public function isShowHDPlugin() {
    return $this->_translation['hd_disabled'] > 0 ? false : true;
  }

  function decorate() {

    $file360p = $this->getRawFile(self::HD_360P);
    $file720p = $this->getRawFile(self::HD_720p);
    $translation = $this->getTranslation();

    $hlsFile = $this->_hls->signFile($file360p);
    $rtspFile = $this->_rtsp->signFile($file360p);
    $rtmpFile360p = $this->_rtmp->signFile($file360p);
    $rtmpFile720p = $this->_rtmp->signFile($file720p);
    $hdsFile = $this->_hds->signFile($file360p);
    $hdsFile2 = $this->_hds->signFile($file720p);

    /*if ($noSign) {
        \vdl::video_stats_add('hits_unsigned');

        $hlsFile = \vdl\wmsmb_hls::sign_file($raw_file, $media);
        $rtmp_settings = \vdl\spb_rtmp::rtmp_settings($raw_file, $media);
        $rtspFile = \vdl\wmsmb_rtsp::sign_file($raw_file, $media);
    }*/

    $provider = $this->_rtmp->getProvider();
    $streamer = $this->_rtmp->getStreamer();

    $stretching = $this->getStretching();
    $autoStart = $this->getAutoStart();
    $duration = $this->getDuration();
    $start = $this->getStart();

    $iframe = new \VideoAdmin\Model\Translation\WidgetIframe($translation);

    $link = $translation->getLink();

    $ova_tag_pre  = $translation['ova_url'] ? str_replace('&', ',', $translation['ova_url']) : null;
    $ova_tag_post = $translation['ova_url_post'] ? str_replace('&', ',', $translation['ova_url_post']) : null;

    $params = array(
      'hls_file'      => $hlsFile,
      'rtsp_file'     => $rtspFile,
      'rtmp_file'     => $rtmpFile360p,
      'rtmp_file2'    => $rtmpFile720p,
      'hds_file'      => $hdsFile,
      'hds_file2'     => $hdsFile2,
      'provider'      => $provider,
      'streamer'      => $streamer,
      'autostart'     => $autoStart,
      'duration'      => $duration,
      'stretching'    => $stretching,
      'start'         => $start,
      'width'         => $this->_width,
      'height'        => $this->_height,
      'translation_id'  => $translation->getId(),
      'title'         => $translation['name'],
      'embedCode'     => addslashes($iframe->decorate()),
      'link'          => $link,
      'showHDPlugin'  => $this->isShowHDPlugin(),
      'showHeaderPlugin' => $this->_sharedLinks,
      'ova_url'       => $ova_tag_pre,
      'ova_url_post'  => $ova_tag_post,
      'image'         => $this->_image,
      'announce'      => $translation->isAnnounce(),
      'media_state'   => $translation['media_state'],
      'start_time'    => date('Y-m-d H:i', $translation['date_start']),
      'channels'      => $translation->getChannels(),
      'currentChannel' => !empty($_GET['channel']) ? (int)$_GET['channel'] : 1,
    );

    if ($stretching) {
      $params['stretching'] = $stretching;
    }

    return \Template::show(
      $this->_template,
      $params
    );
  }

}