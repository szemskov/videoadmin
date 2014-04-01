rtmp://<?= $this->url ?>/vod/<?= $this->publishPoint . '_' . $this->quality . '_001.mp4' ?>:
<div id="player_archive"></div>
<script type="text/javascript" src="<?= $this->libSrc ?>"></script>
<script>
    jwplayer("player_archive").setup({
        "flashplayer":"http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/sbplayer.swf",
        "provider": "rtmp",
        'streamer': 'rtmp://<?= $this->url ?>/vod',
        //_001 - при каждом новом старте постфикс увеличивается
        'file': '<?= $this->publishPoint . '_' . $this->quality . '_001.mp4' ?>'
    });
</script>