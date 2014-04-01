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

if (!empty($_GET['hd'])) {
    $file = $this->rtmp_file2;
} else {
    $file = $this->rtmp_file;
}

//убрали для flash-плеера
/*modes: [
    { type: 'flash', src: "http://russiasport.ru/sites/all/libraries/mediaplayer-5.6/sbplayer.swf" },
    { type: 'html5', config:{"file":"<?= $this->hls_file ?>","provider":"video"} }
]*/
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
        "image":"<?= $this->image ?>",
        "controlbar": "bottom",
        "logo.out": 1,
        "plugins": {
            <? if (!empty($_GET['sochi2014']) && $this->showHDPlugin) : ?>
            "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/hd.swf": {
                "files_360p":"<?= $this->rtmp_file ?>",
                "files_720p":"<?= $this->rtmp_file2 ?>"
            },
            <? endif; ?>
            <? if ($this->showHeaderPlugin) : ?>
            "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/Header.swf": {
                "author":"",
                "title":"",
                "author_link":"",
                "title_link":"",
                "related": "",
                "share":"http://russiasport.ru<?= $this->link ?>",
                "embed":"<?= $this->embedCode ?>"
            },
            <? endif; ?>
            "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/RussiaSport.swf": {"link":"http://russiasport.ru"},
            "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/TimeSliderTooltipPlugin.swf": {"fontcolor":"0xFFFFFF"},
            <? if ($this->ova_url || $this->ova_url_post) : ?>
            "http://videoadmin.russiasport.ru/js/ova-jw.swf": {
                "ads": {
                    "schedule": [
                        <? if ($this->ova_url) : ?>
                        {
                            "position": "pre-roll",
                            "tag": "<?= $this->ova_url ?>"
                        },
                        <? endif; ?>
                        <? if ($this->ova_url_post) : ?>
                        {
                            "position": "post-roll",
                            "tag": "<?= $this->ova_url_post ?>"
                        }
                        <? endif; ?>
                    ],
                    "skipAd": {
                        "enabled": true,
                        "image": "http://videoadmin.russiasport.ru/img/skip_ad.png",
                        "width": 263,
                        "height": 37
                    },
                    "clickSign": {
                        "html": "<p>Перейти на сайт рекламодателя</p>",
                        "horizontalAlign": "center",
                        "width": 220
                    }
                }
            }
      <? endif; ?>
        }
    };
    </script>
</head>
<body style="margin:0px 0px 0px 0px;">
    <div id="<?= $playerId ?>">Ваш браузер не поддерживает воспроизведение видео.</div>
    <script type="text/javascript">
        var setup, settings = <?= $jsSettings ?>;
        if (navigator.userAgent.match(/Android 3|Android 4/i)) {
            setup = cp(settings, visSettings, {
                "modes": [
                    { type: 'html5', config:{"file":"<?= $this->rtsp_file ?>","provider":"video"} }
                ]
            });
        } else if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
            setup = cp(settings, visSettings, {
                modes: [
                    { type: 'html5', config:{"file":"<?= $this->hls_file ?>","provider":"video"} }
                ]
            });
        } else {
            setup = cp(settings, visSettings, {
                provider: '<?= $this->provider ?>',
                streamer: '<?= $this->streamer ?>',
                file: '<?= $file ?>'
            });
        }
        jwplayer("<?= $playerId ?>").setup(setup);
    </script>

    <?php include dirname(__FILE__).'/translation_channels.tpl.php'; ?>
</body>
</html>
