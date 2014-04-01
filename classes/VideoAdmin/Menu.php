<?php

namespace VideoAdmin;

class Menu {

    public static $items = array(
        'list-translation' => array(
            'title' => 'список трансляций',
            'url' => '/translation',
            'active' => true
        ),
        'add-translation' => array(
            'title' => 'добавить трансляцию',
            'url' => '/translation/add',
            'active' => false
        ),
        'list-line' => array(
            'title' => 'список линий',
            'url' => '/line',
            'active' => false
        ),
        'add-line' => array(
            'title' => 'добавить линию',
            'url' => '/line/add',
            'active' => false
        ),
        'list-filter' => array(
            'title' => 'список фильтров',
            'url'   => '/filter',
          'active' => false
        ),
        'add-filter' => array(
          'title' => 'добавить фильтр',
          'url' => '/filter/add',
          'active' => false
        ),
        'add-blacklist' => array(
          'title' => 'создать black-лист IP',
          'url' => '/blacklist/add',
          'active' => false
        ),
        'list-blacklist' => array(
          'title' => 'black-листы IP',
          'url' => '/blacklist',
          'active' => false
        ),
    );

    public static function getItems() {
        return self::$items;
    }

    public static function getDefaultUrl() {
        return '/translation';
    }

}