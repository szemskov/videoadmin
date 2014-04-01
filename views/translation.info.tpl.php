<?php

$data = array(
  'announce' => (int)$this->announce,
  'media_state' => $this->media_state,
  'start_time' => $this->start_time,
  'rtmp' => $this->streamer . '/' . $this->rtmp_file,
  'rtmp_hd' => $this->streamer . '/' . $this->rtmp_file2,
  'rtsp' => $this->rtsp_file,
  'hls'  => $this->hls_file,
  'hds'  => $this->hds_file,
  'hds_hd'  => $this->hds_file2,
  'ova_url' => $this->ova_url,
  'ova_url_post' => $this->ova_url_post,
  'title' => $this->title
);

if ($channels = $this->channels) {
    $data['channels'] = array();
    foreach ($channels as $channel) {
        if (empty($channel['name'])) {
            continue;
        }

        $data['channels'][] = array(
            'channel' => (int)$channel['channel'],
            'name' => $channel['name'],
            'translation_id' => !empty($channel['child_translation_id'])
                ? $channel['child_translation_id']
                : $channel['translation_id']
        );
    }
}

echo json_encode($data);

?>