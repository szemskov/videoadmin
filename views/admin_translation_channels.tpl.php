<?
for ($i = 2; $i <= 4; $i++) {
    if (!empty($this->channels[$i])) {
        $channel = $this->channels[$i];
    } else {
        $channel = array(
            'child_translation_id' => 0,
            'name' => '',
            'line_id' => 0,
            'quadro' => 0
        );
    }
?>
    <div class="form-element">
        <label for="channel-<?= $i ?>-name">Канал <?= $i ?>:</label>
        <input type="text" name="channels[<?= $i ?>][name]" id="channel-<?= $i ?>-name" value="<?= htmlspecialchars($channel['name']) ?>" class="form-element-input" />
    </div>

    <div class="form-element">
        <label for="channel-<?= $i ?>-line_id">Линия <?= $i ?>:</label>
        <select name="channels[<?= $i ?>][line_id]" id="channel-<?= $i ?>-line_id" class="form-element-input">
            <option value="">-выберите линию-</option>
            <? foreach ($this->lines as $item) : ?>
                <option value="<?= $item['id'] ?>" <?= ($channel['line_id'] == $item['id'] ? 'selected' : '') ?>><?= $item['name'] ?></option>
            <? endforeach; ?>
        </select>
    </div>

    <div class="form-element">
        <label style="margin:2em 0 0;">
            <input type="checkbox" name="channels[<?= $i ?>][quadro]" value="1" <? if ($channel['quadro']) echo 'checked="checked"' ?> style="vertical-align:top;" />
            <span style="margin-left:2px;">Квадратор</span>
        </label>
    </div>

    <div class="clearer"></div>
<?
}
?>
