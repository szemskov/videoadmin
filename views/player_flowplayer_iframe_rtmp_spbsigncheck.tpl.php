<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru" xml:lang="ru">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" href="/css/player-styles/style.css" />
  <script src="/js/jquery-1.9.min.js"></script>
  <script src="/flowplayer/flowplayer-3.2.12.min.js"></script>
  <script type="text/javascript">
    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  </script>
</head>
<body style="margin:0px 0px 0px 0px;">
<script>
if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {

  document.write('<div id="dvr-player" data-analytics="UA-29716776-1"><video width="<?= $this->width ?>" height="<?= $this->height ?>" controls="controls"><source src="<?= $this->hls_file ?>" type="application/x-mpegURL" /></video></div>');

} else if (navigator.userAgent.match(/Android 3|Android 4/i)) {

  document.write('<div id="dvr-player" data-analytics="UA-29716776-1"><video src="<?= $this->rtsp_file ?>" width="<?= $this->width ?>" height="<?= $this->height ?>" controls></video></div>');

} else {

  document.write('<div id="dvr-player" style="width:<?= $this->width ?>px; height:<?= $this->height ?>px" data-analytics="UA-29716776-1"></div>');

  config = {
    key: '#$97e8b267d536c204532',
    analytics: "UA-29716776-1",
    plugins: {
      controls:null,
      debug: 'none',
      rs_controls:{
        url: "/flowplayer/flowplayer.rs.controls-0.3.swf",
<? if (!empty($_GET['sochi2014']) && $this->showHDPlugin): ?>
        hd_labels:{
          hd:"720p",
          sd:"360p"
        },
<? else: ?>
        hd_labels:{
          hd:"",
          sd:""
        },
<? endif; ?>
        fontsize: 11,
        fontcolor: "0xffffff",
        position: "over",
        rs_url: "http://russiasport.ru/",
        width: '100%',
        height: 27,
        left: 0,
        margin: 0,
        bottom: 0
      },
      /*"rs-header": {
        url:"/flowplayer/flowplayer.rs.header-0.3.swf",
        author:"Russiasport",
        title:"<?= $this->title ?>",
        title_link: "http://russiasport.ru/",
        top: 0,
        left: 0,
        width:'100%',
        height:'100%'
      },*/
      <? if ($this->ova_url || $this->ova_url_post) : ?>
      ova: {
        "url": "/flowplayer/ova.swf",
        "autoPlay": true,

        "player": {
          "setUrlResolversOnAdClips": false,
          "modes": {
            "linear": {
              "controls": {
                "enable": false
              }
            }
          }
        },
        "ads": {
          "pauseOnClickThrough": true,
          "setDurationFromMetaData" : true,
          "deriveShowDurationFromMetaData" : true,

          "servers": [],
          "schedule": [
            <? if ($this->ova_url) : ?> {
              "position": "pre-roll",
              "server": {
                "type": "direct",
                "tag": "<?= $this->ova_url ?>"
              }
            }, <? endif; ?><? if ($this->ova_url_post) : ?> {
              "position": "post-roll",
              "server": {
                "type": "direct",
                "tag": "<?= $this->ova_url_post ?>"
              }
            }, <? endif; ?>
            {}
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
      },
      <? endif; ?>
      controlbar:{
        margin:-10
      }
    }
  };

<? if ($this->setevizor) : ?>

  var errorHtml = '<p>Для просмотра видео <a href="http://download.setevisor.tv" target="_blank">установите</a> интернет-транслятор "ТВим" компании "Сетевизор".<br/><a href="http://download.setevisor.tv" target="_blank">http://download.setevisor.tv</a></p>';

  config.plugins["rtmp"] = {
    objectEncoding: "0",
    netConnectionUrl: "<?= $this->streamer ?>",
    url: "/flowplayer/flowplayer.rtmp-3.2.12.swf"
  };

  config.clip = {
    url: "<?= $this->rtmp_file2 ?>",
    autoPlay: true,
    provider: '<?= $this->provider ?>'
  };

  config.onError = function(code, msg) {
    if (code == 200 || code == 201 || code == 202) {
      $("#dvr-player").remove();
      $("body").append(errorHtml);
    }
  };

<? else: ?>
<?
    $hds_file = $this->hds_file;
    $hds_file2 = $this->hds_file2;
    $regex = '#^(.+)/mp4:(.+)$#';
    if (preg_match($regex, $hds_file, $m1) && preg_match($regex, $hds_file2, $m2)) {
        $baseUrl = $m1[1];
        $hds_file = 'mp4:'.$m1[2];
        $hds_file2 = 'mp4:'.$m2[2];;
    }

    if (!empty($_GET['hd']) || $this->isQuadro) {
        $file = $hds_file2;
        $hd = 'true';
    } else {
        $file = $hds_file;
        $hd = 'false';
    }
?>
  config.plugins["f4m"] = {
    url: "/flowplayer/flowplayer.f4m-3.2.9.swf",
    dvrBufferTime: 12,
    liveBufferTime: 12,
    startLivePosition: true,
    dvrSnapToLiveClockOffset: 12
  };

  config.plugins["httpstreaming"] = {
    url: "/flowplayer/flowplayer.httpstreaming-3.2.10.swf"
  };

  config.clip = {
<? if (isset($baseUrl)): ?>
    baseUrl: '<?= $baseUrl ?>',
<? endif; ?>
    autoPlay: true,
    urlResolvers: ['f4m'],
    provider: 'httpstreaming'
  };

  config.playlist = [
    {
      url: "<?= $file ?>",
      customProperties:{
        hd: <?= $hd; ?>
      }
    }
  ];

<? endif; ?>

  var pageUrl;
  try {
    pageUrl = top.location.toString();
  } catch (e) {
<?php if (!empty($_SERVER['HTTP_REFERER'])): ?>
    pageUrl = <?php echo json_encode($_SERVER['HTTP_REFERER']); ?>;
<?php else: ?>
    pageUrl = location.toString();
<?php endif; ?>
  }

  var _tracker;
  function getTracker() {
    if (!_tracker && typeof _gat !== 'undefined') {
      _tracker = _gat._getTracker('UA-29716776-1');
    }
    return _tracker;
  }

  function trackEvent(action) {
    var tracker = getTracker();
    if (tracker) {
      var time = parseInt(flowplayer().getTime());
      tracker._trackEvent("Flowplayer", action, pageUrl, time);
    }
  }

  config.clip.onStart = function() {
    trackEvent("Start");
  };
  config.clip.onSeek = function() {
    trackEvent("Seek");
  };
  config.clip.onPause = function() {
    trackEvent("Pause");
  };
  config.clip.onResume = function() {
    trackEvent("Resume");
  };

  config.clip.scaling = 'fit';

  flowplayer("dvr-player", "/flowplayer/flowplayer.commercial-3.2.16.swf", config);
}
</script>

<?php include dirname(__FILE__).'/translation_channels.tpl.php'; ?>

</body>
</html>










