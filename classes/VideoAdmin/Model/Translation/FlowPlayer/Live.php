<?php

namespace VideoAdmin\Model\Translation\FlowPlayer;

class Live extends \VideoAdmin\Model\Translation\FlowPlayer\PlayerAbstract implements \VideoAdmin\Decorator\DecoratorInterface {

  public function getRawFile($quality = null) {
    if (null === $quality) {
      $quality = $this->getQuality();
    }
    return $this->_translation->getPublishPoint() . '_' . $quality;
  }

  public function decorate() {
    return parent::decorate();
  }

  public function __toString() {
    return $this->decorate();
  }

}
