<?php

$hls = str_replace(array('/dvr/', '/dvr_redirect/'), '/Choh6kahbodvr/', preg_replace('/\?.*/', '', $this->hls_file));
$rtsp = str_replace(array('/dvr/','/dvr_redirect/'), '/Choh6kahbodvr/', preg_replace('/\?.*/', '', $this->rtsp_file));

//$hls = str_replace(array('/dvr/', '/dvr_redirect/'), '/Choh6kahbodvr/', $this->hls_file);
//$rtsp = str_replace(array('/dvr/','/dvr_redirect/'), '/Choh6kahbodvr/', $this->rtsp_file);

$data = array(
  'rtmp' => $this->streamer . '/' . $this->rtmp_file,
  'rtsp' => $rtsp,
  'hls'  => $hls,
  'hds'  => $this->hds_file
);

echo json_encode($data);

?>