<?php

namespace VideoAdmin\Model\Line;

class Item extends \VideoAdmin\Model\ItemAbstract {

    public function __toString() {
        return $this['name'];
    }

}