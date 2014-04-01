<?php
/**
 * Тестовый срипт для проверки записи данных скриптом evs.php
 *
 */


function sendPostData($data) {
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, "http://videoadmin.russiasport.ru/evs.php");
  curl_setopt($ch, CURLOPT_POST, count($data));
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
  curl_setopt($ch, CURLOPT_HEADER, 1);
  curl_setopt($ch, CURLOPT_USERPWD, "video300:oZoongohsaePho2");

  //execute post
  $result = curl_exec($ch);
  $responseCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

  curl_close($ch);
  return $responseCode == 200 ? $result : false;
}

$result = sendPostData(array(
  'logsheet_uuid' => '7a6dc787-d0e1-4894-8e9a-6f48d1f8e0b0',
  'logsheet_keywords'  => 'палки, лыжи, проход, гол',
  'logsheet_time' => '16:43:09:18',
  'logsheet_date' => '2013-04-20T00:00:00'
));

var_dump($result);