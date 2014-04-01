<div id="msg-container" class="jGrowl"></div>
<script type="text/javascript">
    $(document).ready(function() {

        $.jGrowl.defaults.position = 'center-center';
        $.jGrowl.defaults.life = 10000;

        $(".js-delete").button({
            icons: {
                primary: "ui-icon-trash"
            },
            text: false
        }).click(function() {
            if (confirm('Удалить выбранную запись?')) {
                location.href = $(this).attr('data-url');
            }
        });

        $(".js-video").button({
            icons: {
                primary: "ui-icon-video"
            },
            text: false
        }).click(function() {
            var button = $(this);

            $(".js-dialog").trigger('init', [
                button.attr('data-id'),
                button.attr('data-title')
            ]);
        });

        $(".js-play").button({
            icons: {
                primary: "ui-icon-play"
            },
            text: false
        }).click(function() {
            if (!confirm('Вы уверены, что хотите запустить трансляцию?')) {
                return false;
            }

            var button = $(this);
            var stopButton = $('.js-stop[data-id=' + button.attr('data-id') + ']');

            stopButton.button('option', 'disabled', false);
            button.button('option', 'disabled', true);

            $.ajax({
                url: button.attr('data-url'),
                success: function(data) {
                    $('#msg-container').jGrowl('Трансляция запущена');
                    button.trigger('callbackSuccess');
                },
                error: function() {
                    $('#msg-container').jGrowl('Произошла ошибка! Попробуйте запустить трансляцию еще раз');
                    button.trigger('callbackError');

                    //stopButton.button('option', 'disabled', true);
                    //button.button('option', 'disabled', false);
                }
            });
        }).each(function(i) {
            var disabled = parseInt( $(this).attr('data-disabled') );
            $(this).button('option', 'disabled', disabled && 1);
        });

        $(".js-stop").button({
            icons: {
                primary: "ui-icon-stop"
            },
            text: false
        }).click(function() {
            if (!confirm('Вы уверены, что хотите остановить трансляцию?')) {
                return false;
            }

            var button = $(this);
            var startButton = $('.js-play[data-id=' + button.attr('data-id') + ']');

            startButton.button('option', 'disabled', false);
            button.button('option', 'disabled', true);

            $.ajax({
                url: button.attr('data-url'),
                success: function(data) {
                    $('#msg-container').jGrowl('Трансляция остановлена');
                    button.trigger('callbackSuccess');
                },
                error: function() {
                    $('#msg-container').jGrowl('Произошла ошибка! Попробуйте остановить трансляцию еще раз');
                    button.trigger('callbackError');

                    //startButton.button('option', 'disabled', true);
                    //button.button('option', 'disabled', false);
                }
            });
        }).each(function(i) {
            var disabled = parseInt( $(this).attr('data-disabled') );
            $(this).button('option', 'disabled', disabled);
        });

        $(".js-dialog").bind("init", function(e, id, title) {
            var url = '/player/admin/' + id;
            var box = $(".js-dialog-content");

            var frame = $(".js-dialog-content iframe").clone(true);
            box.empty();

            $(this).dialog({
                title: title,
                width:700,
                open: function() {
                    frame.attr('src', url).appendTo(box);
                },
                close: function() {
                    frame.attr('src', null);
                }
            });
        });
    });
</script>