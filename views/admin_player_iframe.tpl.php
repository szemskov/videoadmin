<div class="js-player-iframe" style="float:left">
    <iframe hspace="0" vspace="0" marginheight="0" marginwidth="0" src="<?= $this->url ?>" frameborder="0" height="480" scrolling="no" width="540"></iframe>
</div>
<? if (!$this->embed) : ?>
<script type="text/javascript">
$(document).ready(function() {
    //960x540
    var iframe, label, cnt = 1;

    $(".js-play").bind("callbackSuccess", function() {
        createIframe($(this).attr("data-id"));
    });

    $(".js-stop").bind("callbackSuccess", function() {
        updateIframe($(this).attr("data-id"));
    });

    function createIframe(id) {
        if (!iframe) {
            iframe = $(".js-player-iframe iframe").clone(true);
            $(".js-player-iframe").append(iframe);
        }
        iframe.attr('src', "/player/admin/" + id + '?' + cnt++);
    }

    function updateIframe(id) {
        if (iframe) {
            iframe.attr('src', "/player/admin/" + id + '?' + cnt++);
        }
    }

    <? if (!$this->announce) : ?>
        createIframe(<?= $this->translation_id ?>);
    <? endif; ?>
});
</script>
<? endif; ?>