<?php

namespace VideoAdmin\Model\Translation;

/**
 * Показывает код вставки плеера
 */
class WidgetIframe implements \VideoAdmin\Decorator\DecoratorInterface  {

    protected $_item = null;

    protected $_host = null;

    public function __construct(\VideoAdmin\Model\Translation\Item $item) {
        $this->_item = $item;
        $this->_host = \Registry::get('HOST');
    }

    public function decorate() {
        return \Template::show('widget', array(
            'host' =>   $this->_host,
            'id'    => $this->_item['id']
        ));
    }

    public function __toString() {
        return (string)$this->decorate();
    }

    public function setOptions(array $options) {
      return $this;
    }

}