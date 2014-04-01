<?php

namespace VideoAdmin\Controller;

class Translation extends ControllerAbstract {

    protected static $_mediaStates = array(
        \VideoAdmin\Model\Translation::STATE_ANNOUNCE    => 'анонс',
        \VideoAdmin\Model\Translation::STATE_LIVE        => 'live',
        \VideoAdmin\Model\Translation::STATE_ARCHIVE     => 'архив',
    );

    public function add() {
        $name = null;
        $dateStart = null;
        $cdn = true;
        $ova_url = null;
        $ova_url_post = null;
        $ova_url_sochi = null;
        $ova_url_post_sochi = null;

        $format_3x4 = null;
        $hd_disabled = null;
        $node_id = null;
        $dvr = null;
        $setevizor = null;
        $check_geoip = true;

        $logSheetId = null;
        $keywords = null;

        $content = null;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = new \VideoAdmin\Model\Translation();

            $data = $this->_prepareData($_POST);

            $name = $data['name'];
            $dateStart = $data['date_start'];
            $lineId = $data['line_id'];
            $cdn = $data['cdn'];
            $ova_url = $data['ova_url'];
            $ova_url_post = $data['ova_url_post'];
            $ova_url_sochi = $data['ova_url_sochi'];
            $ova_url_post_sochi = $data['ova_url_post_sochi'];

            $format_3x4 = $data['format_3x4'];
            $hd_disabled = $data['hd_disabled'];
            $node_id = $data['node_id'];
            $dvr = $data['dvr'];
            $logSheetId = $data['logsheet_id'];
            $keywords = $data['keywords'];
            $setevizor = $data['setevizor'];
            $check_geoip = $data['check_geoip'];

            $error = false;

            if (empty($name)) {
                $content .= $this->_showError('Укажите название трансляции');
                $error = true;
            }

            if (empty($dateStart)) {
                $content .= $this->_showError('Укажите дату и время начала трансляции');
                $error = true;
            }

            if (!$error) {

                $observerNode = new \VideoAdmin\Model\Translation\Observer\Node('add');
                $model->attach($observerNode);

                $observerFilter = new \VideoAdmin\Model\Translation\Observer\Filter();
                $model->attach($observerFilter);

                $item = $model->add($name, $dateStart, $lineId, $cdn, $ova_url, $ova_url_post, $ova_url_sochi, $ova_url_post_sochi, $node_id, $dvr, $keywords);

                if ($item) {

                    $item->setFields(array(
                        'format_3x4'  => $format_3x4,
                        'hd_disabled' => $hd_disabled,
                        'setevizor'   => $setevizor,
                        'check_geoip' => $check_geoip,
                    ));

                    if ($logSheetId) {
                      $item->setLogSheetId($logSheetId);
                    }

                    if (!empty($_POST['channels'])) {
                        $model->saveChannels($item['id'], $_POST['channels']);
                    }

                    $this->_redirect('/translation/edit/' . $item['id']);
                } else {
                    $content .= $this->_showError('Произошла ошибка при добавлении трансляции');
                }
            }
        }

        $content .= $this->_showForm( 0, $name, $dateStart, null, null, $cdn, $ova_url, $ova_url_post,
                                      $format_3x4, $hd_disabled, $node_id, $dvr, $logSheetId, $keywords, null,
                                      $setevizor, null, null, $check_geoip);

        $this->_layout($content, 'Создание трансляции');
    }

    public function edit() {
        $id = (int)$_GET['id'];

        $model = new \VideoAdmin\Model\Translation();

        $translation = $model->getItem($id);

        $name = $translation['name'];
        $dateStart = $translation['date_start'];
        $lineId = $translation['line_id'];
        //$statusId = $translation['media_state'];
        $cdn = $translation['cdn'];

        $ova_url = $translation['ova_url'];
        $ova_url_post = $translation['ova_url_post'];
        $ova_url_sochi = $translation['ova_url_sochi'];
        $ova_url_post_sochi = $translation['ova_url_post_sochi'];

        $format_3x4 = $translation['format_3x4'];
        $hd_disabled = $translation['hd_disabled'];
        $node_id = $translation['node_id'];
        $dvr  = $translation['dvr'];
        $logSheetId = $translation->getLogSheetId();
        $keywords = $translation['keywords'];
        $setevizor = $translation['setevizor'];
        $check_geoip = $translation['check_geoip'];

        $content = null;
        $user = \VideoAdmin\Model\User::getInstance();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $data = $this->_prepareData($_POST);

            $name = $data['name'];
            $dateStart = $data['date_start'];
            $lineId = $data['line_id'];
            //$statusId = $data['media_state'];
            $cdn = $data['cdn'];

            $ova_url = $data['ova_url'];
            $ova_url_post = $data['ova_url_post'];
            $ova_url_sochi = $data['ova_url_sochi'];
            $ova_url_post_sochi = $data['ova_url_post_sochi'];

            $format_3x4 = $data['format_3x4'];
            $hd_disabled = $data['hd_disabled'];
            $node_id = $data['node_id'];
            $dvr = $data['dvr'];
            $logSheetId = $data['logsheet_id'];
            $keywords = $data['keywords'];
            $setevizor = $data['setevizor'];
            $check_geoip = $data['check_geoip'];

            $error = false;

            if (empty($name)) {
                $content .= $this->_showError('Укажите название трансляции');
                $error = true;
            }

            if (empty($dateStart)) {
                $content .= $this->_showError('Укажите дату и время начала трансляции');
                $error = true;
            }

            if (!$model->checkAccessRights($translation, $user)) {
                $content .= $this->_showError('У вас нет прав для редактирования трансляции');
                $error = true;
            }

            if (!$error) {

                $observerNode = new \VideoAdmin\Model\Translation\Observer\Node('edit');
                $model->attach($observerNode);

                $observerFilter = new \VideoAdmin\Model\Translation\Observer\Filter();
                $model->attach($observerFilter);

                if (!empty($_POST['channels'])) {
                    $model->saveChannels($id, $_POST['channels']);
                }

                $item = $model->edit($id, $name, $dateStart, $lineId, $cdn, $ova_url, $ova_url_post, $ova_url_sochi, $ova_url_post_sochi, $node_id, $dvr, $keywords);

                /*if ($statusId > 0) {
                    $model->changeState($id, $statusId);
                }*/

                if ($item) {

                    $item->setFields(array(
                      'format_3x4'  => $format_3x4,
                      'hd_disabled' => $hd_disabled,
                      'setevizor'   => $setevizor,
                      'check_geoip' => $check_geoip,
                    ));

                    $item->setLogSheetId($logSheetId);

                    $this->_redirect("/translation/edit/$id");
                } else {
                    $content .= $this->_showError('Произошла ошибка при добавлении трансляции');
                }
            }
        }

        $stat = $translation->getHits();

        $content .= $this->_showForm( $id, $name, $dateStart, $lineId, null, $cdn, $ova_url, $ova_url_post, $ova_url_sochi, $ova_url_post_sochi,
                                      $format_3x4, $hd_disabled, $node_id, $dvr, $logSheetId, $keywords, null, $setevizor,
                                      $translation->getIframe(), $translation['online'], $check_geoip
                                    );

        $content .= $this->_showButtons($translation);

        //плеер по умолчанию
        $content .= \Template::show('admin_translation_links', array(
          'node_id' => $translation->getNodeId(),
          'translation_id' => $translation->getId()
        ));

        $content .= \Template::show('admin_translation_stat', array('stat' => $stat, 'item' => $translation));

        $content .= \Template::show('admin_translation_channels_stat', array(
            'stats' => $translation->getChannelHits(),
            'channels' => $translation->getChannels(),
            'items' => $translation->getChannelItems()
        ));

        $content .= \Template::show('admin_player_iframe', array(
            'url' => "/player/admin/$id?test=1",
            'embed' => false,
            'translation_id' => $id,
            'announce' => $translation->isAnnounce()
        ));

        $this->_layout($content, 'Редактирование трансляции');
    }

    public function delete() {
        $id = (int)$_GET['id'];

        $model = new \VideoAdmin\Model\Translation();
        $translation = $model->getItem($id);
        $user = \VideoAdmin\Model\User::getInstance();

        if ($model->checkAccessRights($translation, $user)) {
          $observerNode = new \VideoAdmin\Model\Translation\Observer\Node('delete');
          $model->attach($observerNode);

          $success = $model->delete($id);

          if ($success) {
            $this->_redirect();
          }
        } else {
          header('HTTP/1.1 403 Forbidden', true, 403);
        }
    }

    public function index() {

        $model = new \VideoAdmin\Model\Translation();

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 0;

        if ($page <= 0) {
            $page = 1;
        }

        $limit = 15;
        $offset = $limit * ($page - 1);

        $filter = $this->_getFilter();

        $items = $model->getItems(null, null, $limit, $offset, true, $filter);

        $content = $this->_showItems($items);
        $pager = $this->_showPager($limit, $page, $model->countTotal(true, $filter), '/translation/page', $filter);

        $this->_layout($content . $pager, 'Список трансляций');
    }

    public function start() {
        $id = (int)$_GET['id'];

        $model = new \VideoAdmin\Model\Translation();

        $translation = $model->getItem($id);

        $result = $translation->start();

        if ($result) {
            $channels = $translation->getChannels();
            foreach ($channels as $channel) {
                if (!empty($channel['child_translation_id'])) {
                    $child = $model->getItem($channel['child_translation_id']);
                    $child->start();
                }
            }
        } else {
            header('HTTP/1.1 500 Server error', true, 500);
        }

        echo $result;
    }

    public function stop() {
        $id = (int)$_GET['id'];

        $model = new \VideoAdmin\Model\Translation();

        $translation = $model->getItem($id);

        $result = $translation->stop();

        $channels = $translation->getChannels();
        foreach ($channels as $channel) {
            if (!empty($channel['child_translation_id'])) {
                $child = $model->getItem($channel['child_translation_id']);
                $child->stop();
            }
        }

        echo $result;
    }

    protected function _prepareData($input) {
        $name = !empty($input['name']) ? trim($input['name']) : null;
        $dateStart = !empty($input['date_start']) ? $input['date_start'] : null;
        $timeStart = !empty($input['time_start']) ? $input['time_start'] : null;
        $lineId = !empty($input['line_id']) ? (int)$input['line_id'] : null;
        $statusId = !empty($input['media_state']) ? (int)$input['media_state'] : null;
        $cdn = !empty($input['cdn']) ? (int)$input['cdn'] : 0;
        $ova_url = !empty($input['ova_url']) ? trim($input['ova_url']) : null;
        $ova_url_post = !empty($input['ova_url_post']) ? trim($input['ova_url_post']) : null;
        $ova_url_sochi = !empty($input['ova_url_sochi']) ? trim($input['ova_url_sochi']) : null;
        $ova_url_post_sochi = !empty($input['ova_url_post_sochi']) ? trim($input['ova_url_post_sochi']) : null;
        $format_3x4 = !empty($input['format_3x4']) ? (int)$input['format_3x4'] : 0;
        $hd_disabled = !empty($input['hd_disabled']) ? (int)$input['hd_disabled'] : 0;
        $node_id = !empty($input['node_id']) ? (int)$input['node_id'] : null;
        $dvr = !empty($input['dvr']) ? (int)$input['dvr'] : 0;
        $log_sheet_id = !empty($input['logsheet_id']) ? trim($input['logsheet_id']) : null;
        $keywords = !empty($input['keywords']) ? trim($input['keywords']) : null;
        $setevizor = !empty($input['setevizor']) ? (int)$input['setevizor'] : 0;
        $check_geoip = !empty($input['check_geoip']) ? 1 : 0;

        if ($cdn && $cdn != 1) {
          $cdn = 1;
        }

        $time = 0;

        if ($timeStart) {
            $parts = explode(':', $timeStart);
            $h = (int)$parts[0];

            if ($h > 0 && $h < 24) {
                $time = $h * 60 * 60;
            }

            if (isset($parts[1])) {
                $m = (int)$parts[1];

                if ($m > 0 && $m < 60) {
                    $time += $m * 60;
                }
            }
        }

        if ($dateStart) {
            $dateStart = strtotime($dateStart) + $time;
        } elseif ($time > 0) {
            $dateStart = strtotime(date('Y-m-d')) + $time;
        }

        return array(
            'name' => $name,
            'date_start' => $dateStart,
            'line_id' => $lineId,
            'media_state' => $statusId,
            'cdn' => $cdn,
            'ova_url' => $ova_url,
            'ova_url_post' => $ova_url_post,
            'ova_url_sochi' => $ova_url_sochi,
            'ova_url_post_sochi' => $ova_url_post_sochi,
            'format_3x4'    => $format_3x4,
            'hd_disabled'   => $hd_disabled,
            'node_id'       => $node_id,
            'dvr'           => $dvr,
            'logsheet_id'  => $log_sheet_id,
            'keywords'     => $keywords,
            'setevizor'    => $setevizor,
            'check_geoip'  => $check_geoip
        );
    }

    protected function _showForm($id = null,
                                 $name = null,
                                 $dateStart = null,
                                 $lineId = null,
                                 $statusId = null,
                                 $cdn = null,
                                 $ova_url = null,
                                 $ova_url_post = null,
                                 $ova_url_sochi = null,
                                 $ova_url_post_sochi = null,
                                 $format_3x4 = null,
                                 $hd_disabled = null,
                                 $node_id = null,
                                 $dvr = null,
                                 $logSheetId = null,
                                 $keywords = null,
                                 $substitutions = null,
                                 $setevizor = null,
                                 $embedCode = null,
                                 $onlineUsers = null,
                                 $check_geoip = null)
    {
        if ($dateStart) {
            $date = new \DateTime();
            $date->setTimestamp($dateStart);
            $timeStart = $date->format('H:i');
            $dateStart = $date->format('Y-m-d');
        } else {
            $timeStart = null;
        }

        $line = new \VideoAdmin\Model\Line();

        $lines = $line->getItems();

        $model = new \VideoAdmin\Model\Translation();
        $channels = $model->getChannels($id);

        //$states = \VideoAdmin\Model\Translation::getMediaStateList();
        //$statusId = (int)$statusId;

        return \Template::show('admin_translation_form', array(
            'id'            => $id,
            'name'          => $name,
            'date_start'    => $dateStart,
            'time_start'    => $timeStart,
            'line_id'       => $lineId,
            'lines'         => $lines,
            'cdn'           => $cdn,
            'ova_url'       => $ova_url,
            'ova_url_post'  => $ova_url_post,
            'ova_url_sochi' => $ova_url_sochi,
            'ova_url_post_sochi' => $ova_url_post_sochi,
            'format_3x4'    => $format_3x4,
            'hd_disabled'   => $hd_disabled,
            'node_id'       => $node_id,
            'dvr'           => $dvr,
            'logsheet_id'   => $logSheetId,
            'keywords'      => $keywords,
            'substitutions' => $substitutions,
            'setevizor'     => $setevizor,
            'embed_code'    => $embedCode,
            'online_users'  => $onlineUsers,
            'check_geoip'   => $check_geoip,
            'channels' => $channels
        ));
    }

    protected function _showItems(array $items) {

      $filter = array(
          'f_prev' => 0,
          'f_next' => 0
      );

      if (isset($_GET['f_prev'])) {
        $filter['f_prev'] = (int)(bool)$_GET['f_prev'];
      }

      if (isset($_GET['f_next'])) {
        $filter['f_next'] = (int)(bool)$_GET['f_next'];
      }

      $url = '/translation';

		  $item = null;
        ob_start();
        ?>
        <div class="filters">
            <ul>
              <li><a href="<?= $url ?>?f_next=1">Предстоящие</a></li>
              <li><a href="<?= $url ?>?f_prev=1">Прошедшие</a></li>
              <li><a href="<?= $url ?>">Все</a></li>
            </ul>
        </div>
        <br style="clear:both"/>
        <div class="items">
            <table cellspacing="0">
                <tr>
                    <th>Начало</th>
                    <th>Название трансляции</th>
                    <th>Линия</th>
                    <th>Статус</th>
                    <th>NOW</th>
                    <th>MAX</th>
                    <th>Total LIVE</th>
                    <th>Total Архив</th>
                </tr>
                <?
                /** @var \VideoAdmin\Model\Translation\Item $item */
                foreach($items as $item) :

                $stopDisabled = !$item->isLive();

                $startDisabled = $item->isLive();

                ?>
                <tr>
                    <td><nobr><?= date('Y-m-d H:i', $item['date_start']) ?></nobr></td>
                    <td><a href="/translation/edit/<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a></td>
                    <td><?= ($item['line'] ? $item['line'] : 'не указана') ?></td>
                    <td><span class="js-status"><?= self::$_mediaStates[ $item['media_state'] ] ?></span></td>
                    <td><?= $item['online'] ?></td>
                    <td><?= $item['max_views'] ?></td>
                    <td><?= $item['views_live'] ?></td>
                    <td><?= $item['views_archive'] ?></td>
                    <td>
                        <? if ($item['line_id'] > 0) : ?>
                        <button type="button"
                                title="Начать трансляцию"
                                class="js-play"
                                data-url="/translation/start/<?= $item['id'] ?>"
                                data-id="<?= $item['id'] ?>"
                                data-disabled="<?= $startDisabled ? '1' : '0' ?>">
                        </button>
                        <? endif; ?>
                    </td>
                    <td>
                        <? if ($item['line_id'] > 0) : ?>
                        <button type="button"
                                title="Остановить трансляцию"
                                class="js-stop"
                                data-url="/translation/stop/<?= $item['id'] ?>"
                                data-id="<?= $item['id'] ?>"
                                data-disabled="<?= $stopDisabled ? '1' : '0' ?>">
                        </button>
                        <? endif; ?>
                    </td>
                    <td>
                        <? if ($item['line_id'] > 0) : ?>
                        <button type="button" title="Показать плеер" class="js-video" data-title="<?= $item['name'] ?>" data-id="<?= $item['id'] ?>"></button>
                        <? endif; ?>
                    </td>
                    <td><button type="button" title="Удалить трансляцию" class="js-delete" data-url="/translation/delete/<?= $item['id'] ?>"></button></td>
                </tr>
                <? endforeach; ?>
            </table>
        </div>
        <div class="js-dialog">
            <div class="js-dialog-content">
                <?= \Template::show('admin_player_iframe', array('embed' => true, 'width' => 640, 'height' => 480)) ?>
            </div>
        </div>
        <script>
        $(document).ready(function() {


        });
        </script>
        <?
        $content = ob_get_clean();

        $content .= \Template::show('init', array('translation' => $item));

        return $content;
    }

    protected function _redirect($url = null) {
        if (!$url) {
            $url = '/translation';
        }

        header('Location: ' . $url);
    }

    protected function _showButtons($item) {

        $stopDisabled = !$item->isLive();
        $startDisabled = $item->isLive();

        ob_start();
        ?>
        <div class="control-panel">
            <? if ($item['line_id'] > 0) : ?>
            <button type="button"
                    title="Начать трансляцию"
                    class="js-play"
                    data-url="/translation/start/<?= $item['id'] ?>"
                    data-id="<?= $item['id'] ?>"
                    data-disabled="<?= $startDisabled ? '1' : '0' ?>">
            </button>
            <? endif; ?>

            <? if ($item['line_id'] > 0) : ?>
            <button type="button"
                    title="Остановить трансляцию"
                    class="js-stop"
                    data-url="/translation/stop/<?= $item['id'] ?>"
                    data-id="<?= $item['id'] ?>"
                    data-disabled="<?= $stopDisabled ? '1' : '0' ?>">
            </button>
            <? endif; ?>

            <button type="button"
                    title="Удалить трансляцию"
                    class="js-delete"
                    data-url="/translation/delete/<?= $item['id'] ?>">
            </button>
        </div>

        <?
        $content = ob_get_clean();
        $content .= \Template::show('init', array('translation' => $item));

        return $content;
    }

    protected function _getFilter() {

      $filter = array('f_next' => null, 'f_prev' => null);

      $filter = array_intersect_key($_GET, $filter);
      $filter = array_map('intval', $filter);

      $filter['f_parent'] = true;

      return $filter;
    }  
}