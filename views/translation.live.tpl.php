rtmp://<?= $this->url ?>/live/<?= $this->publishPoint . '_' . $this->quality ?>:
<div id="player_live"></div>
<script type="text/javascript" src="<?= $this->libSrc ?>"></script>
<script>
    jwplayer("player_live").setup({
        "flashplayer":"http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/sbplayer.swf",
        "provider": "rtmp",
        'streamer': 'rtmp://<?= $this->url ?>/live',
        'file': '<?= $this->publishPoint . '_' . $this->quality ?>'
    });
</script>