<?php

namespace VideoAdmin\Model\Translation\FlowPlayer;

class Announce extends \VideoAdmin\Model\Translation\FlowPlayer\Live implements \VideoAdmin\Decorator\DecoratorInterface {

    protected $_type = 'live';

    protected $_sharedLinks = false;

    public function getRawFile($quality = null) {
        if (null === $quality) {
            $quality = $this->getQuality();
        }

        $line = $this->_translation->getLine();

        if (!empty($line['stream'])) {
           $name = $line['stream'];
        } else {
           $name = $line['name'] . '_' . $quality;
        }

        return $name;
    }

}