<?php

//define('ROOT_DIR', rtrim(getcwd(), '\/'));
define('ROOT_DIR', dirname(__FILE__));

$_SERVER['HTTP_HOST'] = 'videoadmin.russiasport.ru';

include_once __DIR__ . '/classes/Autoloader.php';
$autoloader = new Autoloader();

include_once __DIR__ . '/init.php';
include_once __DIR__ . '/init_db.php';

define('NG_STAT_LIVE', 'russiasport-live');
define('NG_STAT_ARCHIVE', 'russiasport-vod');

function getData($name) {
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, "https://stats.ngenix.net/automate/get_streams_stat.pl?ntag=" . $name);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_USERPWD, "velkyne@gmail.com:uyjham6q");
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

  //execute post
  return curl_exec($ch);
}

function collectStat($translationType) {

  $data = getData($translationType);

  $xml_parser = xml_parser_create();
  $application = $ntag = '';
  $ret = array();

  xml_set_element_handler($xml_parser,

    // Ловим открывающийся XML тег
    function($parser, $name, $attrs) use (&$application,
      &$ntag, &$ret) {

      // Ловим тип видео
      if ($name == 'NTAG') {
        $ntag = $attrs['NAME'];
        return;
      }

      // Ловим раздающее приложение
      if ($name == 'APPLICATION') {
        $application = $attrs['NAME'];
        return;
      }

      // Ловим собственно статистику показов
      if ($name == 'STREAM') {
        switch($ntag) {
          case NG_STAT_LIVE:
            $regexp = '#(?<publication_point>\d{12}[a-zA-Z0-9]+)(_?(?<quality>\d?))$#';
            break;
          case NG_STAT_ARCHIVE:
            // '#^(?<client>russiasport)\/(?<publication_point>\d+_[a-zA-Z0-9]+)(_?(?<quality>\d?))_(?<version>\d+)\.(?<exstension>\w+)$#';
            $regexp = '#(?<publication_point>\d{12}[a-zA-Z0-9]+)(_?(?<quality>\d?))$#';
            break;
        }

        $src = explode('|', $attrs['NAME']);
        $src = array_shift($src);

        if (!preg_match($regexp, $src, $matches)) {
          echo 'Video name invalid format: '.$attrs['NAME'];
        }
        else {
          //$ret[$matches['publication_point']][$application][$matches['quality']] = $attrs['CONNECTIONS'];
          if (!isset($ret[$matches['publication_point']])) {
            $ret[$matches['publication_point']] = (int)$attrs['CONNECTIONS'];
          } else {
            $ret[$matches['publication_point']] += (int)$attrs['CONNECTIONS'];
          }
        }
      }
    },

    function() {}
  );

  if (!xml_parse($xml_parser, $data)) {
    xml_parser_free($xml_parser);
    throw Exception('Ошибка парсинга XML');
  }
  xml_parser_free($xml_parser);

  return $ret;
}

\Logger::write('time: ' . date('Y-m-d H:i:s'), 'ngenix-stat');

$model = new \VideoAdmin\Model\Translation();
$model->resetStats();

//сбор статистики с NGENIX
foreach (array(NG_STAT_LIVE, NG_STAT_ARCHIVE) as $type) {
  $data = collectStat($type);

  foreach ($data as $key => $value) {
    $model->updateStats($key, $value);
  }

  \Logger::write('data: ' . var_export($data, true), 'ngenix-stat');
}

//сбор статистики с вовзы в оверсане

$wowza = new \VideoAdmin\Wowza\Server();
$data = $wowza->getStats();
foreach ($data as $key => $value) {
    $model->updateStats($key, $value);
}
\Logger::write('data: ' . var_export($data, true), 'oversun-stat');

/*$translations = $model->getItems(null, null, null, null, false, array(
  'f_cdn' => false,
  'f_announce' => false
));

// @var \VideoAdmin\Model\Translation\Item $item 
foreach ($translations as $item) {
  if ($item['cdn'] && !$item->isArchive()) {
      continue;
  }

  $pp = $item['play_point'];  

  if ($item->isLive()) {
    if ($item['dvr']) {
      $app = 'dvredge';
    } else {
      $app = 'edge';
    }
  } else {
    $app = 'vod';
  }

  $count = $wowza->getStats($pp, $app);
  $model->updateStats($pp, $count);

  $data[$pp] = $count;

  usleep(5000);
}

\Logger::write('data: ' . var_export($data, true), 'ngenix-stat');*/




