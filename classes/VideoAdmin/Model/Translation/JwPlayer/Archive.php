<?php

namespace VideoAdmin\Model\Translation\JwPlayer;

class Archive extends \VideoAdmin\Model\Translation\JwPlayer\PlayerAbstract implements \VideoAdmin\Decorator\DecoratorInterface {

    const STRETCHING = 'uniform'; //'uniform';

    public function decorate() {
        /*return \Template::show('translation.archive', array(
            'url' => \Registry::get('WOWZA_URL'),
            'libSrc' => \Registry::get('PLAYER_LIB'),
            'publishPoint' => $this->_translation['play_point'],
            'quality' => $this->_quality
        ));*/
        return parent::decorate();
    }

    public function __toString() {
        return $this->decorate();
    }

    public function getStretching() {
        return self::STRETCHING;
    }

    public function getRawFile($quality = null) {
        if (null === $quality) {
            $quality = $this->getQuality();
        }

        $file = $this->_translation->getPublishPoint();

        if ($this->isShowHDPlugin()) {
          $file .= '_' . $quality . '_001.mp4';
        } else {
          $file .= '_001.mp4';
        }

        return $file;
    }

}