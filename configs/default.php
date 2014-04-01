<?php

return array(
    //локальный конфиг
    'videoadmin.local.ru' => array(
        'DB' => array(
            'name' => 'videoadmin',
            'host' => 'videoadmin.local.ru',
            'port' => 3306,
            'user' => 'root',
            'password' => 'velk07)$81'
        ),
        'API' => array(
            //'host' => 'http://dev300:ras-by-wyt-hayn-ewd-om@russiasport.webta.ru',
            'host' => 'http://russiasport.local.ru',
            'path' => '/mod_evs/process'
        ),
        'WOWZA' => array(
            //'ip_list' => array('188.127.244.2', '188.127.244.9'),
            'ip_list' => array('188.127.244.22'),
            'host'  => 'http://188.127.244.2:1935',
            'password' => 'qw762hgd',

            //использовать ngenix cdn
            'cdn' => false,

            //включить/отключить проверку подписи
            'sign' => true,

            'rtmp'  => array(
              'live'    => 'rtmp://russiasport-live.cdn.ngenix.net/aT8thoozeerdct/_definst_',
              'vod'     => 'rtmp://russiasport-vod.cdn.ngenix.net/svod_redirect/_definst_',

              //адрес при отключенном cdn
              'local'   => 'rtmp://188.127.244.2:1935'
            ),

            'hls'   => array(
              'live'   => 'http://russiasport-live.cdn.ngenix.net/aT8thoozeerdct/_definst_',
              'vod'    => 'http://russiasport-vod.cdn.ngenix.net/un4Aewo7utrdct/_definst_',
              'local'   => 'http://188.127.244.2',
              'dvr'    => 'http://russiasport-live.cdn.ngenix.net/Choh6kahbodvr/_definst_'
            ),

            'rtsp'  => array(
              'live'   => 'rtsp://russiasport-live.cdn.ngenix.net/aT8thoozee/_definst_',
              'vod'    => 'rtsp://russiasport-vod.cdn.ngenix.net/un4Aewo7ut/_definst_',
              'local' => 'rtsp://188.127.244.2:1935',
              'dvr'   => 'rtsp://russiasport-live.cdn.ngenix.net/Choh6kahbodvr/_definst_'
            ),

            'hds'  => array(
              'dvr'   => 'http://russiasport-live.cdn.ngenix.net/Choh6kahbodvr/_definst_',
              'live'   => 'http://russiasport-live.cdn.ngenix.net/slive/_definst_',
              'vod'    => 'http://russiasport-vod.cdn.ngenix.net/svod/_definst_',
              'local'   => 'http://188.127.244.2'
            )
        ),
        'GROUP_ID' => 226,
        'PLAYER' => array(
          'type' => 'jw'
        ),
        'USER' => 'test',
        'LOG_PATH' => false,
        'GEOIP' => array(
          'CHECK_COUNTRY_CODE' => true,
          'ALLOWED_CODES' => array('RU')
        ),
        'CACHE' => false,
        'MEMCACHE_SERVERS' => array(
          '127.0.0.1:11211',
        )
    ),
    'videoadmin.local' => array(
        'DB' => array(
            'name' => 'videoadmin',
            'host' => 'localhost',
            'port' => 3306,
            'user' => 'videoadmin',
            'password' => 'neegei1uWe6gaet'
        ),
        'API' => array(
            'host' => 'http://russiasport.local',
            'path' => '/mod_evs/process'
        ),
        'WOWZA' => array(
            //'ip_list' => array('188.127.244.2', '188.127.244.9'),
            'ip_list' => array('188.127.244.22'),
            'host'  => 'http://188.127.244.2:1935',
            'password' => 'qw762hgd',

            //использовать ngenix cdn
            'cdn' => false,

            //включить/отключить проверку подписи
            'sign' => true,

            'rtmp'  => array(
              'live'    => 'rtmp://russiasport-live.cdn.ngenix.net/slive_redirect/_definst_',
              'vod'     => 'rtmp://russiasport-vod.cdn.ngenix.net/svod_redirect/_definst_',

              //адрес при отключенном cdn
              'local'   => 'rtmp://188.127.244.2:1935'
            ),

            'hls'   => array(
              'live'   => 'http://russiasport-live.cdn.ngenix.net/aT8thoozeerdct/_definst_',
              'vod'    => 'http://russiasport-vod.cdn.ngenix.net/un4Aewo7utrdct/_definst_',
              'local'   => 'http://188.127.244.2',
              'dvr'    => 'http://russiasport-live.cdn.ngenix.net/Choh6kahbodvr/_definst_'
            ),

            'rtsp'  => array(
              'live'   => 'rtsp://russiasport-live.cdn.ngenix.net/aT8thoozee/_definst_',
              'vod'    => 'rtsp://russiasport-vod.cdn.ngenix.net/un4Aewo7ut/_definst_',
              'local' => 'rtsp://188.127.244.2:1935',
              'dvr'   => 'rtsp://russiasport-live.cdn.ngenix.net/Choh6kahbodvr/_definst_'
            ),

            'hds'  => array(
              'dvr'    => 'http://russiasport-live.cdn.ngenix.net/dvr/_definst_',
              'live'   => 'http://russiasport-live.cdn.ngenix.net/dvr/_definst_',
              'vod'    => 'http://russiasport-vod.cdn.ngenix.net/svod/_definst_',
              'local'  => 'http://188.127.244.2'
            )
        ),
        'GROUP_ID' => 226,
        'PLAYER' => array(
          'type' => 'jw'
        ),
        'LOG_PATH' => false,
        'USER' => 'test',
        'GEOIP' => array(
          'CHECK_COUNTRY_CODE' => true,
          'ALLOWED_CODES' => array('RU')
        ),
        'CACHE' => true,
        'CACHE_TYPE' => 'memcache',
        'MEMCACHE_SERVERS' => array(
          '127.0.0.1:11211',
        )
    ),
    'localhost:82' => array(
        'DB' => array(
            'name' => 'videoadmin',
            'host' => 'localhost',
            'port' => 3306,
            'user' => 'root',
            'password' => '1'
        ),
        'API' => array(
            'host' => 'http://russiasport.local.ru',
            'path' => '/mod_evs/process'
        ),
        'WOWZA' => array(
            //'ip_list' => array('188.127.244.2', '188.127.244.9'),
            'ip_list' => array('188.127.244.22'),
            'host'  => 'http://188.127.244.2:1935',
            'password' => 'qw762hgd',

            //использовать ngenix cdn
            'cdn' => false,

            //включить/отключить проверку подписи
            'sign' => true,

            'rtmp'  => array(
              'live'    => 'rtmp://russiasport-live.cdn.ngenix.net/slive_redirect/_definst_',
              'vod'     => 'rtmp://russiasport-vod.cdn.ngenix.net/svod/_definst_',

              //адрес при отключенном cdn
              'local'   => 'rtmp://188.127.244.2:1935'
            ),

            'hls'   => array(
              'live'   => 'http://russiasport-live.cdn.ngenix.net/slive_redirect/_definst_',
              'vod'    => 'http://russiasport-vod.cdn.ngenix.net/svod_redirect/_definst_',
              'local'   => 'http://188.127.244.2',
              'dvr'    => 'http://russiasport-live.cdn.ngenix.net/dvr_redirect/_definst_'
            ),

            'rtsp'  => array(
              'live'   => 'rtsp://russiasport-live.cdn.ngenix.net/slive/_definst_',
              'vod'    => 'rtsp://russiasport-vod.cdn.ngenix.net/svod_redirect/_definst_',
              'local' => 'rtsp://188.127.244.2:1935',
              'dvr'   => 'rtsp://russiasport-live.cdn.ngenix.net/dvr/_definst_'
            ),

            'hds'  => array(
              'dvr'   => 'http://russiasport-live.cdn.ngenix.net/dvr/_definst_',
              'live'   => 'http://russiasport-live.cdn.ngenix.net/slive_redirect/_definst_',
              'vod'    => 'http://russiasport-vod.cdn.ngenix.net/svod_redirect/_definst_',
              'local'   => 'http://188.127.244.2'
            )
        ),
        'GROUP_ID' => 226,
        'PLAYER' => array(
          'type' => 'jw'
        ),
        'LOG_PATH' => false,
        'GEOIP' => array(
          'CHECK_COUNTRY_CODE' => true,
          'ALLOWED_CODES' => array('RU')
        )
    ),
    //тестовый конфиг
    'videoadmin.webta.ru' => array(
        'DB' => array(
            'name' => 'videoadmin_20131115',
            'host' => 'localhost',
            'port' => 3306,
            'user' => 'videoadmin',
            'password' => 'neegei1uWe6gaet'
        ),
        'API' => array(
            'host' => 'http://dev300:ras-by-wyt-hayn-ewd-om@russiasport.webta.ru',
            'path' => '/mod_evs/process'
        ),
        'WOWZA' => array(
            //'ip_list' => array('188.127.244.2', '188.127.244.9'),
            'ip_list' => array('188.127.244.22'),
            'host'  => 'http://188.127.244.2:1935',
            'password' => 'qw762hgd',

            //использовать ngenix cdn
            'cdn' => false,

            //включить/отключить проверку подписи
            'sign' => true,

            'rtmp'  => array(
              'live'    => 'rtmp://russiasport-live.cdn.ngenix.net/slive_redirect/_definst_',
              'vod'     => 'rtmp://russiasport-vod.cdn.ngenix.net/svod_redirect/_definst_',

              //адрес при отключенном cdn
              'local'   => 'rtmp://188.127.244.2:1935'
            ),

            'hls'   => array(
              'live'   => 'http://russiasport-live.cdn.ngenix.net/aT8thoozeerdct/_definst_',
              'vod'    => 'http://russiasport-vod.cdn.ngenix.net/un4Aewo7utrdct/_definst_',
              'local'   => 'http://188.127.244.2',
              'dvr'    => 'http://russiasport-live.cdn.ngenix.net/Choh6kahbodvr/_definst_'
            ),

            'rtsp'  => array(
              'live'   => 'rtsp://russiasport-live.cdn.ngenix.net/aT8thoozee/_definst_',
              'vod'    => 'rtsp://russiasport-vod.cdn.ngenix.net/un4Aewo7ut/_definst_',
              'local' => 'rtsp://188.127.244.2:1935',
              'dvr'   => 'rtsp://russiasport-live.cdn.ngenix.net/Choh6kahbodvr/_definst_'
            ),

            'hds'  => array(
              'dvr'   => 'http://russiasport-live.cdn.ngenix.net/Choh6kahbodvr/_definst_',
              'live'   => 'http://russiasport-live.cdn.ngenix.net/Choh6kahbodvr/_definst_',
              'vod'    => 'http://russiasport-vod.cdn.ngenix.net/svod/_definst_',
              'local'   => 'http://188.127.244.2'
            )
        ),
        'GROUP_ID' => 226,
        'PLAYER' => array(
          'type' => 'jw'
        ),
        'LOG_PATH' => false,
        'USER' => 'test',
        'GEOIP' => array(
          'CHECK_COUNTRY_CODE' => true,
          'ALLOWED_CODES' => array('RU')
        ),
        'CACHE' => true,
        'CACHE_TYPE' => 'memcached',
        'MEMCACHE_SERVERS' => array(
          '127.0.0.1:11211'
        )
    ),
    //продакшн
    'videoadmin.russiasport.ru' => array(
        'DB' => array(
            'name' => 'videoadmin',
            'host' => 'db.russiasport.ru',
            'port' => 3306,
            'user' => 'videoadmin',
            'password' => 'neegei1uWe6gaet'
        ),
        'API' => array(
            'host' => 'http://russiasport.ru',
            'path' => '/mod_evs/process'
        ),
        'WOWZA' => array(
            //'ip_list' => array('188.127.244.2', '188.127.244.9'),
            'ip_list' => array('188.127.244.22'),
            'host'  => 'http://188.127.244.2:1935',
            'password' => 'qw762hgd',

            //использовать ngenix cdn
            'cdn' => false,

            //включить/отключить проверку подписи
            'sign' => true,

            'rtmp'  => array(
              'live'    => 'rtmp://russiasport-live.cdn.ngenix.net/slive_redirect/_definst_',
              'vod'     => 'rtmp://russiasport-vod.cdn.ngenix.net/svod_redirect/_definst_',

              //адрес при отключенном cdn
              'local'   => 'rtmp://188.127.244.2:1935'
            ),

            'hls'   => array(
              'live'   => 'http://russiasport-live.cdn.ngenix.net/aT8thoozeerdct/_definst_',
              'vod'    => 'http://russiasport-vod.cdn.ngenix.net/un4Aewo7utrdct/_definst_',
              'local'   => 'http://188.127.244.2',
              'dvr'    => 'http://russiasport-live.cdn.ngenix.net/Choh6kahbodvr/_definst_'
            ),

            'rtsp'  => array(
              'live'   => 'rtsp://russiasport-live.cdn.ngenix.net/aT8thoozee/_definst_',
              'vod'    => 'rtsp://russiasport-vod.cdn.ngenix.net/un4Aewo7ut/_definst_',
              'local' => 'rtsp://188.127.244.2:1935',
              'dvr'   => 'rtsp://russiasport-live.cdn.ngenix.net/Choh6kahbodvr/_definst_'
            ),

            'hds'  => array(
              'dvr'    => 'http://russiasport-live.cdn.ngenix.net/dvr/_definst_',
              'live'   => 'http://russiasport-live.cdn.ngenix.net/slive/_definst_',
              'vod'    => 'http://russiasport-vod.cdn.ngenix.net/svod/_definst_',
              'local'  => 'http://188.127.244.2'
            )
        ),
        //'GROUP_ID' => 722938
        'GROUP_ID' => 226,
        'PLAYER' => array(
          'type' => 'jw'
        ),
        'LOG_PATH' => '/var/www/tmp/videoadmin/logs',
        'USER' => 'test',
        'GEOIP' => array(
          'CHECK_COUNTRY_CODE' => true,
          'ALLOWED_CODES' => array('RU')
        ),
        'CACHE' => true,
        'CACHE_TYPE' => 'memcached',
        'MEMCACHE_SERVERS' => array(
          'vip1.russiasport.ru:11211',
          'vip2.russiasport.ru:11211',
          'vip3.russiasport.ru:11211',
          'vip4.russiasport.ru:11211',
          'vip5.russiasport.ru:11211',
          'vip6.russiasport.ru:11211',
          'vip7.russiasport.ru:11211',
          'vip8.russiasport.ru:11211',
          'vip9.russiasport.ru:11211',
        )
    )
);
