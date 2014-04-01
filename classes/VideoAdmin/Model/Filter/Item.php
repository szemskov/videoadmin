<?php

namespace VideoAdmin\Model\Filter;

class Item extends \VideoAdmin\Model\ItemAbstract {

    public function __toString() {
        return $this['name'];
    }

}