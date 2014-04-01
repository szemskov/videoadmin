<?php

$settings = array(
    'width' => $this->width,
    'height' => $this->height,
    'stretching' => $this->stretching,
    'autostart' => $this->autostart,
);

if ($this->duration) {
    $settings['duration'] = $this->duration;
}

if ($this->start) {
    $settings['start'] = $this->start;
}

$jsSettings = json_encode($settings);

$playerId = 'translation_' . $this->translation_id;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru" xml:lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <style>
        body {color: #5A7087; font-size: 1em; font-family: Arial;}
    </style>
    <link rel="stylesheet" type="text/css" href="/css/player-styles/style.css" />
    <script type="text/javascript" src="<?= \Registry::get('PLAYER_LIB') ?>"></script>
    <script type="text/javascript">
        function cp(obj) {
            for (var i = 1; i < arguments.length; i++) {
                var arg = arguments[i];
                for (var p in arg) if (arg.hasOwnProperty(p)) {obj[p] = arg[p];}
            }
            return obj;
        }

        var visSettings = {
            "skin": "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/ruskin/ruskin.xml",
            "flashplayer":"http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/sbplayer.swf",
            "image":"",
            "controlbar": "bottom"
        };
    </script>
</head>
<body style="margin:0px 0px 0px 0px;">
<div id="<?= $playerId ?>">Ваш браузер не поддерживает воспроизведение видео.</div>
<script type="text/javascript">
    var setup;
    settings = <?= $jsSettings ?>;

    setup = cp(settings, visSettings, {
        provider: '<?= $this->provider ?>',
        streamer: '<?= $this->streamer ?>',
        file: '<?= $this->rtmp_file ?>',
        modes: [
            { type: 'flash', src: "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/sbplayer.swf" },
            { type: 'html5', config:{"file":"<?= $this->hls_file ?>","provider":"video"} }
        ]
    });
    jwplayer("<?= $playerId ?>").setup(setup);

</script>

<?php include dirname(__FILE__).'/translation_channels.tpl.php'; ?>

</body>
</html>










