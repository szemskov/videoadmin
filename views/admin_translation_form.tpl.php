<form method="POST" id="translation_form">
    <div class="form-element">
        <label for="name">Название:</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($this->name) ?>" class="form-element-input form-element-large" />
    </div>

    <div class="form-element">
        <label for="date_start">Дата начала:</label>
        <input type="text" name="date_start" id="date_start" value="<?= htmlspecialchars($this->date_start) ?>" placeholder="yyyy-mm-dd"  class="form-element-input form-element-medium"  />
    </div>

    <div class="form-element">
        <label for="time_start">Время начала:</label>
        <input type="text" name="time_start" id="time_start" value="<?= htmlspecialchars($this->time_start) ?>" placeholder="00:00"  class="form-element-input form-element-small"  />
    </div>

    <div class="clearer"></div>

    <div class="form-element">
        <label for="">Канал 1:</label>
        <input type="text" name="channels[1][name]" id="" value="<? if (isset($this->channels[1]['name'])) echo htmlspecialchars($this->channels[1]['name']); ?>" class="form-element-input" />
    </div>

    <div class="form-element">
        <label for="line_id">Линия 1:</label>
        <select name="line_id" id="line_id" class="form-element-input">
            <option value="">-выберите линию-</option>
            <? foreach ($this->lines as $item) : ?>
                <option value="<?= $item['id'] ?>" <?= ($this->line_id == $item['id'] ? 'selected' : '') ?>><?= $item['name'] ?></option>
            <? endforeach; ?>
        </select>
    </div>

    <div class="form-element">
        <label for="cdn">Использовать NGENIX (для LIVE-трансляций):</label>
        <input type="checkbox" name="cdn" id="cdn" value="1" <?= ($this->cdn ? 'checked' : '') ?> />
    </div>

    <div class="clearer"></div>

<?php include dirname(__FILE__).'/admin_translation_channels.tpl.php'; ?>

    <div class="form-element">
        <label for="ova_url">OVA url:</label>
        <input type="text" name="ova_url" id="ova_url" value="<?= htmlspecialchars($this->ova_url) ?>" class="form-element-input form-element-large"  />
    </div>

    <div class="clearer"></div>

    <div class="form-element">
        <label for="ova_url_post">OVA post url:</label>
        <input type="text" name="ova_url_post" id="ova_url_post" value="<?= htmlspecialchars($this->ova_url_post) ?>" class="form-element-input form-element-large"  />
    </div>

    <div class="clearer"></div>

    <div class="form-element">
        <label for="ova_url_sochi">OVA url SOCHI:</label>
        <input type="text" name="ova_url_sochi" id="ova_url_sochi" value="<?= htmlspecialchars($this->ova_url_sochi) ?>" class="form-element-input form-element-large"  />
    </div>

    <div class="clearer"></div>

    <div class="form-element">
        <label for="ova_url_post_sochi">OVA post url SOCHI:</label>
        <input type="text" name="ova_url_post_sochi" id="ova_url_post_sochi" value="<?= htmlspecialchars($this->ova_url_post_sochi) ?>" class="form-element-input form-element-large"  />
    </div>

    <!--<div class="form-element">
        <label for="media_state">Статус:</label>
        <select name="media_state" id="media_state" class="form-element-input">
            <?/* foreach ($states as $state => $stateName) : */?>
            <option value="<?/*= $state */?>" <?/*= ($state == $statusId ? 'selected' : '') */?>><?/*= $stateName */?></option>
            <?/* endforeach; */?>
        </select>
    </div>-->

    <div class="clearer"></div>

    <div class="form-element">
        <label for="check_geoip">Геоблокировка:</label>
        <input type="checkbox" name="check_geoip" id="check_geoip" value="1" <?= ($this->check_geoip ? 'checked' : '') ?> />
    </div>

    <div class="form-element">
        <label for="format_3x4">Формат 4x3:</label>
        <input type="checkbox" name="format_3x4" id="format_3x4" value="1" <?= ($this->format_3x4 ? 'checked' : '') ?> />
    </div>

    <div class="form-element">
        <label for="hd_disabled">Отключить HD:</label>
        <input type="checkbox" name="hd_disabled" id="hd_disabled" value="1" <?= ($this->hd_disabled ? 'checked' : '') ?> />
    </div>

    <div class="form-element">
        <label for="dvr">Трансляция DVR:</label>
        <input type="checkbox" name="dvr" id="dvr" value="1" <?= ($this->dvr ? 'checked' : '') ?> />
    </div>

    <div class="form-element">
        <label for="setevizor">Сетевизор (для LIVE-трансляций):</label>
        <input type="checkbox" name="setevizor" id="setevizor" value="1" <?= ($this->setevizor ? 'checked' : '') ?> />
    </div>

    <div class="clearer"></div>
    <br/>

    <div class="form-element">
        <label for="node_id">Node ID:</label>
        <input type="text" name="node_id" id="node_id" value="<?= htmlspecialchars($this->node_id) ?>" class="form-element-input"  />
    </div>

    <div class="clearer"></div>
    <br/>

    <div class="form-element">
        <label for="logsheet_id">LogSheet ID:</label>
        <input type="text" name="logsheet_id" id="logsheet_id" value="<?= htmlspecialchars($this->logsheet_id) ?>" class="form-element-input form-element-large"  />
    </div>

    <div class="clearer"></div>

    <br/>

    <div class="form-element">
        <label for="keywords">Фильтр меток:</label>
        <textarea name="keywords" id="keywords" class="form-element-large" style="height: 140px"><?= htmlspecialchars($this->keywords) ?></textarea>

        <select id="keywords_list">
            <option value="">-выбрать из списка-</option>
        </select>
    </div>

    <div class="clearer"></div>
    <br/>

    <? if ($this->embed_code) : ?>
        <div class="form-element">
            <label for="logsheet_id">Код вставки:</label>
            <textarea id="embed_code" class="form-element-large"><?= htmlspecialchars($this->embed_code) ?></textarea>
            <button id="embed_code_button" type="button" style="width:175px; height:32px" data-clipboard-target='embed_code'>Копировать в буфер</button>
        </div>

        <div class="clearer"></div>
        <br/>
    <? endif; ?>

    <input type="submit" value="Сохранить" />
</form>

<script type="text/javascript" src="/js/ZeroClipboard.min.js"></script>
<script>
    $(document).ready(function() {
        $(function() {
            $( "#date_start" ).datepicker({
                dateFormat: "yy-mm-dd"
            });
        });

        $.ajax({
            dataType: "json",
            url: "/filter/select",
            success: function(data) {
                $.each(data, function(i, item) {
                    if (item.keywords) {
                        $("#keywords_list").append("<option value=\"" + item.keywords + "\">" + item.name + "</option>");
                    }
                });
            }
        });

        $("#keywords_list").change(function() {
            var v = $(this).val()
            if (v) {
                $("#keywords").val(v);
            }
        });

        var clip = new ZeroClipboard($("#embed_code_button"), {
            moviePath: "/js/ZeroClipboard.swf"
        });

        $("#embed_code_button").click(function() {
            clip.setText( $("#embed_code").val() );
        });

        $("#setevizor").change(function() {
            if (this.checked) {
                $("#dvr").get(0).checked = false;
                $("#cdn").get(0).checked = false;
            }
        });

        $("#dvr").change(function() {
            if (this.checked) {
                $("#setevizor").get(0).checked = false;
            }
        });
    });
</script>
<script type="text/javascript" src="http://panoramahd.ru/panoplan/logsheets-selector.js"></script>
