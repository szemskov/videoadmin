<?php

namespace VideoAdmin\Model\Translation\FlowPlayer;

abstract class PlayerAbstract extends \VideoAdmin\Model\Translation\PlayerAbstract {

  const TEMPLATE = 'player_flowplayer_iframe_rtmp_spbsigncheck';

  protected $_template = 'player_flowplayer_iframe_rtmp_spbsigncheck';

  public function decorate() {
    $file360p = $this->getRawFile(self::HD_360P);
    $file720p = $this->getRawFile(self::HD_720p);
    $translation = $this->getTranslation();

    $hdsFile = $this->_hds->signFile($file360p);
    $hdsFile2 = $this->_hds->signFile($file720p);
    $hlsFile = $this->_hls->signFile($file360p);
    $rtspFile = $this->_rtsp->signFile($file360p);
    $rtmpFile360p = $this->_rtmp->signFile($file360p);
    $rtmpFile720p = $this->_rtmp->signFile($file720p);

    $provider = $this->_rtmp->getProvider();
    $streamer = $this->_rtmp->getStreamer();

    $ova_tag_pre  = $translation['ova_url'] ? str_replace('&', ',', $translation['ova_url']) : null;
    $ova_tag_post = $translation['ova_url_post'] ? str_replace('&', ',', $translation['ova_url_post']) : null;

    /*$ova_tag_pre  = $translation['ova_url'];
    $ova_tag_post = $translation['ova_url_post'];*/

    $params = array(
      'hls_file'      => $hlsFile,
      'rtsp_file'     => $rtspFile,
      'rtmp_file'     => $rtmpFile360p,
      'rtmp_file2'    => $rtmpFile720p,
      'hds_file'      => $hdsFile,
      'hds_file2'      => $hdsFile2,
      'width'         => $this->_width,
      'height'        => $this->_height,
      'translation_id'  => $translation->getId(),
      'title'         => $translation['name'],
      'ova_url'       => $ova_tag_pre,
      'ova_url_post'  => $ova_tag_post,
      'provider'      => $provider,
      'streamer'      => $streamer,
      'image'         => $this->_image,
      'announce'      => $translation->isAnnounce(),
      'media_state'   => $translation['media_state'],
      'start_time'    => date('Y-m-d H:i', $translation['date_start']),
      'setevizor'     => false,
      'channels'      => $translation->getChannels(),
      'currentChannel' => !empty($_GET['channel']) ? (int)$_GET['channel'] : 1,
      'isQuadro'      => $translation->isQuadro()
    );

    return \Template::show(
      $this->_template,
      $params
    );
  }
}