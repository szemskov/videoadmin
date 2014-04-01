<?php

namespace VideoAdmin\Model\Translation\JwPlayer;

class Live extends \VideoAdmin\Model\Translation\JwPlayer\PlayerAbstract implements \VideoAdmin\Decorator\DecoratorInterface {

    const STRETCHING = 'exactfit';

    public function decorate() {
        /*return \Template::show('translation.live', array(
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
        return $this->_translation['format_3x4'] ? 'uniform' : self::STRETCHING;
    }

    public function getRawFile($quality = null) {
        if (null === $quality) {
            $quality = $this->getQuality();
        }

        $file = $this->_translation->getPublishPoint();

        if ($this->isShowHDPlugin()) {
            $file .= '_' . $quality;
        }

        return $file;
    }

}