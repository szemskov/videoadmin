<table cellpadding="5" cellspacing="0" border="1">
    <tr>
        <th width="50">Канал</th>
        <th>Название</th>
        <th width="50">NOW</th>
        <th width="50">MAX</th>
        <th width="50">Total LIVE</th>
        <th width="50">Total Архив</th>
    </tr>
<?
    foreach ($this->items as $key => $item) {
?>
        <tr>
            <td><?= $key ?></td>
            <td><?= htmlspecialchars($this->channels[$key]['name']) ?></td>
            <td><?= $item['online'] ?></td>
            <td><?= $item['max_views'] ?></td>
            <td><?= $this->stats[$key]['views_live'] ?></td>
            <td><?= $this->stats[$key]['views_archive'] ?></td>
        </tr>
<?
    }
?>
</table>
