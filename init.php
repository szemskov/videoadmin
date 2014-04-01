<?php

//конфигурация
$config = include_once ROOT_DIR . '/configs/default.php';
$host = $_SERVER['HTTP_HOST'];

if (!isset($config[$host])) {
    die('Server error');
}

if (!function_exists('geoip_country_code_by_name')) {
	function geoip_country_code_by_name($remote_addr) {
		return 'RU';
	}
}

if(isset($_SERVER['HTTP_AUTHORIZATION'])) {
  $auth_params = explode(":" , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
  $_SERVER['PHP_AUTH_USER'] = $auth_params[0];
  unset($auth_params[0]);
  $_SERVER['PHP_AUTH_PW'] = implode('',$auth_params);
}

$config = $config[$host];

Registry::set('API', $config['API']);

$geoipConfig = array(
  'CHECK_COUNTRY_CODE' => false,
  'ALLOWED_CODES' => array()
);

if (isset($config['GEOIP'])) {
  $geoipConfig = array_merge($geoipConfig, (array)$config['GEOIP']);
}

Registry::set('GEOIP', $geoipConfig);

Registry::set('WOWZA_URL', $config['WOWZA']['host']);
Registry::set('WOWZA_PASSWORD', $config['WOWZA']['password']);
Registry::set('WOWZA', $config['WOWZA']);
Registry::set('PLAYER', $config['PLAYER']);

if (!empty($config['USER'])) {
  Registry::set('USER', $config['USER']);
}

//js-библиотека плеера
Registry::set('PLAYER_LIB', 'http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/jwplayer.js');

//текущий хост
Registry::set('HOST', $host);

//ID группы для публикации трансляций
Registry::set('GROUP_ID', $config['GROUP_ID']);

//логировать работу
Registry::set('LOG', true);

if (!empty($config['LOG_PATH'])) {
  Registry::set('LOG_PATH', $config['LOG_PATH']);
}

Registry::set('PLAYER_PATH', 'http://dev300:ras-by-wyt-hayn-ewd-om@russiasport.webta.ru');

if (!empty($config['CACHE'])) {
  if (isset($config['MEMCACHE_SERVERS']) && is_array($config['MEMCACHE_SERVERS'])) {
    \VideoAdmin\Model\MemcacheInit::setServers( $config['MEMCACHE_SERVERS'] );
  }

  $cacheType = 'memcache';

  if (!empty($config['CACHE_TYPE'])) {
    $cacheType = $config['CACHE_TYPE'];
  }

  switch ($cacheType) {
    case 'memcached':
      $cache = new \VideoAdmin\Model\Memcached();
    break;

    case 'memcache':
      $cache = new \VideoAdmin\Model\Memcache();
    break;

    default:
      $cache = new \VideoAdmin\Model\CacheMock();
  }

} else {
  $cache = new \VideoAdmin\Model\CacheMock();
}
Registry::set('CACHE', $cache);

$ip = \Network::getClientIp();
Registry::set('CLIENT_IP', $ip);
