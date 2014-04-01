<?php

namespace VideoAdmin\Model\Translation;

class WidgetAnnounce implements \VideoAdmin\Decorator\DecoratorInterface {

    protected $_translation = null;

    public function __construct(\VideoAdmin\Model\Translation\Item $translation) {
        $this->_translation = $translation;
    }

    public function decorate() {
        return \Template::show('translation.announce', array(
            'date' => $this->_translation['date_start']
        ));
    }

    public function __toString() {
        return $this->decorate();
    }

}