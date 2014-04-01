<?php

namespace VideoAdmin\Model\Translation;

/**
 * Рисует плеер
 */
class WidgetPlayer implements \VideoAdmin\Decorator\DecoratorInterface {

    const DEFAULT_QUALITY_NUM = 1;

    /**
     * @var \VideoAdmin\Decorator\DecoratorInterface
     */
    protected $_translation = null;

    protected $_quality = null;

    protected $_template = null;

    protected $_image = null;

    protected $_json = false;

    public function __construct(\VideoAdmin\Model\Translation\Item $item, $quality = null) {
        $this->_translation = $item;
        $this->_quality = $quality;
    }

    public function decorate() {

        $player = PlayerFactory::getPlayer($this->_translation);

        if ($player) {
            if ($this->_quality > 0) {
              $player->setQuality($this->_quality);
            }

            if ($this->_template) {
              $player->setTemplate($this->_template);
            }

            if ($this->_image) {
              $player->setImage($this->_image);
            }

            return $player->decorate();
        } else {
            //вывод анонса трансляции
            $widget = new \VideoAdmin\Model\Translation\WidgetAnnounce($this->_translation);
            $content = $widget->decorate();
            if ($this->_json) {
                $content = json_encode($content);
            }
            return $content;
        }
    }

    public function __toString() {
        return (string)$this->decorate();
    }

    public function setOptions(array $options) {
        if (isset($options['template'])) {
          $this->_template = $options['template'];
        }

        if (isset($options['image'])) {
          $this->_image = $options['image'];
        }

        if (isset($options['json'])) {
          $this->_json = $options['json'];
        }

        return $this;
    }

}