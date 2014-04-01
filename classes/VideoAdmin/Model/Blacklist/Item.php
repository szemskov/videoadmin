<?php

namespace VideoAdmin\Model\Blacklist;

class Item extends \VideoAdmin\Model\ItemAbstract {

    public function __toString() {
        return $this['name'];
    }

}