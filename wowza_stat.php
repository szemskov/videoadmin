<?php

//define('ROOT_DIR', rtrim(getcwd(), '\/'));
define('ROOT_DIR', dirname(__FILE__));

$_SERVER['HTTP_HOST'] = 'videoadmin.russiasport.ru';

include_once __DIR__ . '/classes/Autoloader.php';
$autoloader = new Autoloader();

include_once __DIR__ . '/init.php';
include_once __DIR__ . '/init_db.php';

\Logger::write('time: ' . date('Y-m-d H:i:s'), 'ngenix-stat');

$model = new \VideoAdmin\Model\Translation();
$wowza = new \VideoAdmin\Wowza\Server();
$data = array();

$translations = $model->getItems(null, null, null, null, false, array(
  'f_live' => true
));

/** @var \VideoAdmin\Model\Translation\Item $item */
foreach ($translations as $item) {

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

  /*\Logger::write('app: ' . $app, 'ngenix-stat');
  \Logger::write('sd: ' . $sd, 'ngenix-stat');
  \Logger::write('hd: ' . $hd, 'ngenix-stat');*/

  $count = $wowza->getStats($pp, $app);

  $model->updateStats($pp, $count);

  $data[$pp] = $count;
}

\Logger::write('data: ' . var_export($data, true), 'ngenix-stat');


