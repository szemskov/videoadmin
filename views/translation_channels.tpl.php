<?php

$api = \Registry::get('API');
$rsHost = $api['host'];

$u = parse_url($_SERVER['REQUEST_URI']);
if (isset($u['query'])) {
    parse_str($u['query'], $params);
    unset($params['channel']);
} else {
    $params = array();
}

$channels = $this->channels;

if (empty($_GET['sochi2014'])) {
    if ($channels && $channels[$this->currentChannel]['quadro']) {
?>
        <ul id="cams" class="translation-channels quadro" style="width:<?= (int)$this->width ?>px;">
            <li class="active"><a id="cam-all" href="#cam-all">Все камеры</a></li>
            <li><a id="cam-1" href="#cam-1">Камера 1</a></li>
            <li><a id="cam-2" href="#cam-2">Камера 2</a></li>
            <li><a id="cam-3" href="#cam-3">Камера 3</a></li>
            <li><a id="cam-4" href="#cam-4">Камера 4</a></li>
        </ul>
        <script type="text/javascript">
            function setActive(e) {
                var li = e.parentNode;
                var ul = li.parentNode;

                var items = ul.getElementsByTagName('li');
                for (var i = 0; i < items.length; i++) {
                    items[i].className = '';
                }

                li.className = 'active';
            }

            function cam_all() {
                var player = flowplayer();
                player.getScreen().animate({ width: '100%', height: '100%', left: 0, top: 0 });
            }
            function cam_1() {
                var player = flowplayer();
                player.getScreen().animate({ width: '200%', height: '200%', left: 0, top: 0 });
            }
            function cam_2() {
                var player = flowplayer();
                player.getScreen().animate({ width: '200%', height: '200%', right: 0, top: 0 });
            }
            function cam_3() {
                var player = flowplayer();
                player.getScreen().animate({ width: '200%', height: '200%', left: 0, bottom: 0 });
            }
            function cam_4() {
                var player = flowplayer();
                player.getScreen().animate({ width: '200%', height: '200%', right: 0, bottom: 0 });
            }

            document.getElementById('cam-all').onclick = function() {
                setActive(this);
                cam_all();
                return false;
            };
            document.getElementById('cam-1').onclick = function() {
                setActive(this);
                cam_1();
                return false;
            };
            document.getElementById('cam-2').onclick = function() {
                setActive(this);
                cam_2();
                return false;
            };
            document.getElementById('cam-3').onclick = function() {
                setActive(this);
                cam_3();
                return false;
            };
            document.getElementById('cam-4').onclick = function() {
                setActive(this);
                cam_4();
                return false;
            };
        </script>
<?
    } elseif ($this->showHDPlugin) {
        $p = $params;
        unset($p['hd']);
        $url = "?channel={$this->currentChannel}&".http_build_query($p);
        $url = rtrim($url, '&');
        $url = htmlspecialchars($url);
?>
        <ul class="translation-channels" style="width:<?= (int)$this->width ?>px;">
            <li <? if (empty($_GET['hd'])) echo 'class="active"'; ?>><a href="<?= $url ?>&amp;hd=0">Стандартное качество</a></li>
            <li <? if (!empty($_GET['hd'])) echo 'class="active"'; ?>><a href="<?= $url ?>&amp;hd=1">Высокое качество</a></li>
        </ul>
<?
    }
}

if ($channels /*&& count($channels) > 1*/) {
?>
    <ul class="translation-channels" style="width:<?= (int)$this->width ?>px;">
<?php
        foreach ($channels as $key => $channel) {
            if (empty($channel['name'])) {
                continue;
            }

            if ($key == $this->currentChannel) {
?>
                <li class="active"><?php echo htmlspecialchars($channel['name']); ?></li>
<?
            } else {
                $isFirstChannel = !$channel['child_translation_id'];
                $p = $params;
                if ($isFirstChannel && !empty($_GET['ref']) && $_GET['ref'] == 'videoframe') {
                    unset($p['ref']);
                    $url = "{$rsHost}/videoframe.php?id={$channel['translation_id']}&".http_build_query($p);
                } else {
                    unset($p['hd']);
                    $url = "?channel={$key}&".http_build_query($p);
                }
                $url = rtrim($url, '&');
?>
                <li><a href="<?php echo htmlspecialchars($url); ?>" target="_self"><?php echo htmlspecialchars($channel['name']); ?></a></li>
<?
            }
        }
?>
    </ul>
<?
}
