<?php

$apiInfo = \Registry::get('API');

?>
<br style="clear:both"/>
<div>
    <a href="<?= $apiInfo['host'] . '/node/' . $this->node_id ?>" target="_blank">Ссылка на Node</a>
</div>
<br style="clear:both"/>
<div>
  <a href="/videocut/<?= $this->translation_id ?>" target="_blank">Редактировать видео</a>
</div>