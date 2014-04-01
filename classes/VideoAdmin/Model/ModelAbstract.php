<?php

namespace VideoAdmin\Model;

abstract class ModelAbstract {

    public function getItemFromCache($id) {
        $class = get_called_class() . '\Item';
        return \Registry::get("{$class}_{$id}");
    }

    public function addItemToCache(\VideoAdmin\Model\ItemAbstract $item) {
        $id = $item->getId();
        $class = get_called_class() . '\Item';
        \Registry::set("{$class}_{$id}", $item);
    }

}