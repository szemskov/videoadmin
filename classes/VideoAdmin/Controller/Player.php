<?php

namespace VideoAdmin\Controller;

class Player extends ControllerAbstract {

    /**
     * вывод плеера в админке
     */
    public function test() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        $test = isset($_GET['test']) ? (int)$_GET['test'] : null;

        $model = new \VideoAdmin\Model\Translation();

        if (!empty($_GET['channel'])) {
            $currentChannel = (int)$_GET['channel'];
            if ($currentChannel > 1) {
                if ($channelId = $model->getChannelId($id, $currentChannel)) {
                    $id = $channelId;
                }
            }
        }

        $content = null;

        try {

            //всегда проигрывать в плеере трансляцию для мониторига (если текущий статус - анонс)
            if ($test) {
              \Registry::set('translation_test', true);
            }

            $item = $model->getItem($id);

            //тестовый плеер для мониторинга
            if ($test) {
                $player = new \VideoAdmin\Model\Translation\JwPlayer\Announce($item);
                $player->setTemplate('admin_player_default');
                $player->disableCDN(); //->disableSign();
            }
            //плеер для просмотра
            else {
                $player = \VideoAdmin\Model\Translation\PlayerFactory::getPlayer($item);
            }

            if ($player) {
                $player->setWidth(540);
                $player->setHeight(320);
                $content = (string)$player;
            }
        } catch (\Exception $e) {
            //выводим красивый шаблон ошибки
            $content = "Ошибка при показе трансляции";
        }

        ?>
        <div id="player">
            <p>Линия: <?= $item->getLine() ?></p>
            <?= $content ?>
        </div>
        <?
    }

    /**
     * вывод плеера для публичной части сайта
     */
    public function index() {
        $id = (int)$_GET['id'];
        $image = isset($_GET['image']) ? $_GET['image'] : null;
        $getLinks = isset($_GET['links']);
        $getInfo = isset($_GET['info']);

        $model = new \VideoAdmin\Model\Translation();

        if (!empty($_GET['channel'])) {
            $currentChannel = (int)$_GET['channel'];
            if ($currentChannel > 1) {
                if ($channelId = $model->getChannelId($id, $currentChannel)) {
                    $id = $channelId;
                }
            }
        }

        $template = null;
        $json = $getLinks || $getInfo;

        if ($getLinks) {
          \VideoAdmin\Model\Translation\NgenixAbstract::$mobileDevices = false;
          $template = 'translation.links';
        }

        if ($getInfo) {
          $template = 'translation.info';
        }

        if ($json) {
          header('Content-Type: application/json');
          header('Access-Control-Allow-Origin: *');
          header('Access-Control-Allow-Methods: GET');
          header('Cache-Control: private');
        }

        $content = null;

        try {
              //проверка ip в black-листе
              $clientIp = \Registry::get('CLIENT_IP');
              $isIpAllowed = \VideoAdmin\Model\Blacklist::checkIp($clientIp);

              $item = $model->getItem($id);

              if (!empty($_GET['sochi2014'])) {
                  $item->ovaUrlSochi();
              }

              $geoip = \Registry::get('GEOIP');

              //проверка гео-блокировки
              if ($isIpAllowed) {
                if ($geoip['CHECK_COUNTRY_CODE']) {

                  $country = \Network::getCountryCode();

                  if (false == $item['check_geoip'] || in_array($country, $geoip['ALLOWED_CODES'])) {
                    $isIpAllowed = true;
                  } else {
                    $isIpAllowed = false;
                  }

                } else {
                  $isIpAllowed = true;
                }
              }

              if ($isIpAllowed) {
                $content = $item->getWidget('media', array(
                  'template' => $template,
                  'image' => $image,
                  'json' => $json
                ));

                $item->addHit();
              } else {
                throw new \ErrorException(null, 403);
              }

        } catch (\Exception $e) {
            //выводим красивый шаблон ошибки
            switch ($e->getCode()) {
              case 403:
                $content = "<p>Этот контент недоступен в вашем регионе.</p>";
                $content .= "<p>This content is not available in your region or country.</p>";
              break;

              default:
                $content = "Ошибка при показе трансляции";
            }

            if ($json) {
                $content = json_encode($content);
            }
        }

        echo $content;
    }

    public function marks() {
      $id = (int)$_GET['id'];

      if ($id <= 0) {
        return;
      }

      $time = (int)$_GET['time'];

      $model = new \VideoAdmin\Model\Translation();
      $translation = $model->getItemByNodeId($id);

      if (!$translation) {
        header("Status: 404 Not Found", true, 404);
        exit;
      }

      $marks = $translation->getMarksFromCache($time);

      header('Content-Type: application/json');
      header('Access-Control-Allow-Origin: *');
      header('Access-Control-Allow-Methods: GET');
      header('Cache-Control: private');

      echo json_encode($marks);
    }

    public function getDuration() {
        $id = (int)$_GET['id'];

        $model = new \VideoAdmin\Model\Translation();
        $translation = $model->getItem($id);

        if ($translation && $translation['start_live_time'] && $translation['stop_live_time']) {
            $duration = $translation['stop_live_time'] - $translation['start_live_time'];
        } else {
            $duration = null;
        }

        echo json_encode(array(
            'duration' => $duration
        ));
    }

}