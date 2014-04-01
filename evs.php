<?php

/**
 * Этот скрипт запрашивается через POST от EVS-сервера с данными о логшитах
 * Входящий массив: array(
 *      'logsheet_uuid' => ID,
 *      'logsheet_keywords' => список слов через запятую
 *      'logsheet_time' => время в формате '16:43:09:18'
 *      'logsheet_date' => дата в формате '2013-04-20T00:00:00'
 * )
 */

define('ROOT_DIR', dirname(__FILE__));

include_once './classes/Autoloader.php';
$autoloader = new Autoloader();

include_once './init.php';
include_once './init_db.php';

try {

  $data = file_get_contents('php://input');

  \Logger::write('time: ' . date('Y-m-d H:i:s'), 'evslogsheet');
  \Logger::write('input: ' . $data, 'evslogsheet');

  if (empty($data)) {
    throw new ErrorException('Empty input data');
  }

  parse_str($data, $input);

  \Logger::write('data: ' . var_export($input, true), 'evslogsheet');

  if (empty($input['logsheet_uuid']) || empty($input['logsheet_keywords']) || empty($input['logsheet_time']) || empty($input['logsheet_date'])) {
    throw new ErrorException('Input data is wrong');
  }

  $timestamp = dateToTimestamp($input['logsheet_date'], $input['logsheet_time']);

  if (!$timestamp) {
    throw new ErrorException('Input data is wrong: datetime');
  }

  /** @var \PDO $pdo  */
  $pdo = \Registry::get('PDO');

  $sth = $pdo->prepare("INSERT INTO logsheet(`log_id`, `name`, `time`) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`)");
  $sth->execute(array(
    $input['logsheet_uuid'],
    $input['logsheet_keywords'],
    $timestamp,
  ));

  $model = new \VideoAdmin\Model\Translation();

  $logSheetId = $input['logsheet_uuid'];
  $items = $model->getItemsByLogSheetId($logSheetId);

  $input_keywords = array_map('trim', explode(',', mb_strtolower($input['logsheet_keywords'], 'UTF-8')));

  $keywords = array();
  foreach ($items as $item) {
    if ($item['keywords']) {
      $kw = array_map('trim', explode(';', mb_strtolower($item['keywords'], 'UTF-8')));
      $keywords = array_merge($keywords, $kw);
    }
  }

  $compare = empty($keywords) ? true : false;

  if (!$compare && array_intersect($keywords, $input_keywords)) {
    $compare = true;
  }

  if ($compare) {
    $sth = $pdo->prepare("INSERT INTO logsheet_labels(`log_id`, `name`, `time`) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE `name` = VALUES(`name`)");
    $sth->execute(array(
      $input['logsheet_uuid'],
      $input['logsheet_keywords'],
      $timestamp,
    ));
  }

  echo (int)$compare;

} catch (Exception $e) {
  header("HTTP/1.0 404 Not Found");
  $m = $e->getMessage();
  \Logger::write('error: ' . $m, 'evslogsheet');
  echo $m;
  exit;
}

function dateToTimestamp($sDate, $sTime) {
  $tDate = strtotime($sDate);
  $aTime = explode(":", $sTime);
  $time = 0;

  if (count($aTime) == 4) {

    $h = intval($aTime[0]);
    $m = intval($aTime[1]);
    $s = intval($aTime[2]);

    $time = $tDate + ($h * 3600) + ($m * 60) + $s;
  }

  return $time;
};
