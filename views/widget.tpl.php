<?php
/**
 * HTML вывод кода вставки плеера теперь перенесен в шаблон Drupal
 */

$width = $this->width ? $this->width : 960;
$height = $this->height ? $this->height : 540;

?>
<iframe src="http://<?= $this->host ?>/player/<?= $this->id ?>?w=<?= $width ?>&h=<?= $height ?>" hspace="0" vspace="0" marginheight="0" marginwidth="0" frameborder="0" width="<?= $width ?>" height="<?= $height ?>" scrolling="no" allowfullscreen></iframe>