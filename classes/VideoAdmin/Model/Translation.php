<?php

namespace VideoAdmin\Model;

class Translation extends ModelAbstract {

    const STATE_ANNOUNCE = 1;

    const STATE_LIVE = 2;

    const STATE_ARCHIVE = 3;

	  public static $notify = true;

    protected $_table = 'translations';

    protected $_tableLines = 'play_lines';

    protected $_tableViews = 'translation_views';

    protected $_tableLogSheet = 'translation_logsheets';

    protected $_tableChannels = 'translation_channels';

    protected $_storage = null;

    protected static $_channels = array();

    public static function getMediaStateList() {
        return array(
            self::STATE_ANNOUNCE => 'анонс',
            self::STATE_LIVE => 'live',
            self::STATE_ARCHIVE => 'архив',
        );
    }

    public function attach(\VideoAdmin\Observer\ObserverInterface $observer) {
        $storage = $this->_initStorage();
        if (!$storage->contains($observer)) {
            $storage->attach($observer);
        }
        return $this;
    }

    public function notify(\VideoAdmin\Model\ItemAbstract $item) {
        $storage = $this->_initStorage();

        /** @var \VideoAdmin\Observer\ObserverInterface $observer */
        foreach ($storage as $observer) {
            $observer->update($item);
        }

        foreach ($storage as $observer) {
          $storage->detach($observer);
        }
    }

    public function add($name, $dateStart, $lineId, $cdn, $ova_url, $ova_url_post, $ova_url_sochi, $ova_url_post_sochi, $node_id, $dvr, $keywords) {
        /** @var \PDO $pdo */
        $pdo = \Registry::get('PDO');

        $playPoint = $this->newPlayPoint($name, $dateStart);

        /** @var \PDOStatement $stmt */
        $stmt = $pdo->prepare('INSERT INTO ' . $this->_table . '(name, date_start, play_point, line_id, cdn, ova_url, ova_url_post, ova_url_sochi, ova_url_post_sochi, node_id, dvr, keywords, user_name) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

        $result = $stmt->execute(array($name, $dateStart, $playPoint, $lineId, $cdn, $ova_url, $ova_url_post, $ova_url_sochi, $ova_url_post_sochi, $node_id, $dvr, $keywords, User::getInstance()->getLogin()));

        if ($result) {
            $item = $this->getItem( $pdo->lastInsertId() );

            if (self::$notify) {
              $this->notify($item);
            }

            return $item;
        }

        return false;
    }

    public function edit($id, $name, $dateStart, $lineId, $cdn, $ova_url, $ova_url_post, $ova_url_sochi, $ova_url_post_sochi, $node_id, $dvr, $keywords) {
        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');

        /** @var \PDOStatement $stmt */
        $stmt = $pdo->prepare('UPDATE ' . $this->_table . ' SET name = ?, date_start = ?, line_id = ?, cdn = ?, ova_url = ?, ova_url_post = ?, ova_url_sochi = ?, ova_url_post_sochi = ?, node_id = ?, dvr = ?, keywords = ? WHERE id = ?');
        $result = $stmt->execute(array($name, $dateStart, $lineId, $cdn, $ova_url, $ova_url_post, $ova_url_sochi, $ova_url_post_sochi, $node_id, $dvr, $keywords, $id));

        if ($result) {
            $updatedItem = $this->getItem($id, true);

            $channels = $this->getChannels($id);
            foreach ($channels as $key => $channel) {
                if (!empty($channel['child_translation_id'])) {
                    $this->edit($channel['child_translation_id'], $name." - {$channels[$key]['name']}", $dateStart, $channel['line_id'], $cdn, $ova_url, $ova_url_post, $ova_url_sochi, $ova_url_post_sochi, $node_id, 0, $keywords);
                }
            }

            if (self::$notify) {
              $this->notify($updatedItem);
            }

            return $updatedItem;
        }

        return false;
    }

    public function delete($id) {
        $id = (int)$id;

        if (self::$notify) {
          $updatedItem = $this->getItem($id);
          $this->notify($updatedItem);
        }

        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $result = $pdo->query("
            DELETE t, ch
            FROM {$this->_table} t
            LEFT JOIN {$this->_tableChannels} ch
              ON ch.translation_id = t.id
            WHERE t.id={$id}
              OR t.parent_id={$id}
        ");

        return $result->rowCount();
    }

    public function setFields($id, array $fields) {
      $val = array();
      $sql = array();

      $exclude = array('play_point', 'line_id');

      foreach ($fields as $name => $value) {
        if (in_array($name, $exclude)) {
            continue;
        }

        $sql[] = "{$name} = :{$name}";
        $val[$name] = $value;
      }

      $val['id'] = $id;

      $sql = 'UPDATE ' . $this->_table . ' SET ' . implode(',', $sql) . ' WHERE id = :id OR parent_id = :id';

      /** @var \PDO $pdo  */
      $pdo = \Registry::get('PDO');

      /** @var \PDOStatement $stmt */
      $stmt = $pdo->prepare($sql);
      $stmt->execute($val);

      $this->childDisableDvr($id);
    }

    public function childDisableDvr($parent_id) {
        /** @var \PDO $pdo */
        $pdo = \Registry::get('PDO');
        $st = $pdo->prepare("UPDATE {$this->_table} SET dvr = 0 WHERE parent_id = :parent_id");
        $st->execute(array('parent_id' => (int)$parent_id));
    }

    public function setLogSheetId($tid, $logId) {
      /** @var \PDO $pdo  */
      $pdo = \Registry::get('PDO');

      $update = $this->hasLogSheets($tid);

      if ($update) {
        $sql = "UPDATE translation_logsheets SET log_id = :log_id WHERE translation_id = :tid";
      } else {
        $sql = "INSERT INTO translation_logsheets(translation_id, log_id) VALUES (:tid, :log_id)";
      }

      $stmt = $pdo->prepare($sql);

      $stmt->bindParam(':tid', $tid, \PDO::PARAM_INT);
      $stmt->bindParam(':log_id', $logId, \PDO::PARAM_STR);

      $stmt->execute();

      $pdo->query($sql);
    }

    public function getLogSheetId($tid) {
      /** @var \PDO $pdo  */
      $pdo = \Registry::get('PDO');

      $tid = (int)$tid;
      $result = $pdo->query("SELECT log_id FROM translation_logsheets WHERE translation_id = $tid")->fetchColumn();

      return $result;
    }

    public function hasLogSheets($tid) {
        return false !== $this->getLogSheetId($tid);
    }

    public function getItemsByLogSheetId($logId) {
      $sql = "SELECT t.* FROM translations t INNER JOIN translation_logsheets l ON t.id = l.translation_id
              WHERE l.log_id = ?";

      /** @var \PDO $pdo  */
      $pdo = \Registry::get('PDO');

      $sth = $pdo->prepare($sql);
      $sth->execute(array($logId));

      $result = $sth->fetchAll();

      $coll = array();

      foreach ($result as $row) {
        $coll[] = new \VideoAdmin\Model\Translation\Item($row);
      }

      return $coll;
    }

    public function updateLogSheetLabels(\VideoAdmin\Model\Translation\Item $item) {

        if (empty($item['keywords'])) {
          return;
        }

        $keywords = explode(';', $item['keywords']);
        $keywords = array_filter( array_map('trim', $keywords) );

        if (!$keywords) {
          return;
        }

        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');

        $logId = $this->getLogSheetId($item['id']);

        if (!$logId) {
          return;
        }

        $pdo->beginTransaction();

        $sth = $pdo->prepare('DELETE FROM logsheet_labels WHERE log_id = ?');
        $sth->execute(array($logId));

        $sth = $pdo->prepare('SELECT * FROM logsheet WHERE log_id = ?');
        $sth->execute(array($logId));

        $rows = $sth->fetchAll(\PDO::FETCH_ASSOC);

        $insertId = array();

        foreach ($rows as $row) {
          foreach ($keywords as $k) {

            if (!preg_match('/\s+/', $k)) {
              if (preg_match('/\b' . $k . '\b/iu', $row['name'])) {
                $insertId[] = (int)$row['id'];
                break;
              };

            } elseif (false !== mb_stristr($row['name'], $k, null, 'UTF-8')) {
              $insertId[] = (int)$row['id'];
              break;
            }
          }
        }

        if ($insertId) {
            $insertId = implode(',', $insertId);

            $sth = $pdo->prepare("INSERT INTO logsheet_labels(log_id, `name`, `time`) SELECT log_id, `name`, `time` FROM logsheet WHERE log_id = ? and id IN($insertId)");

            $sth->execute(array($logId));
        }

        $pdo->commit();

        //\Logger::write(implode(',', $keywords), 'keywords');

        return true;
    }

    public function getItems($sortBy = null, $sortOrder = null, $limit = null, $offset = null, $checkAccess = true, array $filter = null) {
        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');

        $sql = 'SELECT t.*, l.name AS line, v.views_live, v.views_archive FROM ' . $this->_table . ' AS t LEFT JOIN ' . $this->_tableLines .' AS l
                ON t.line_id = l.id LEFT JOIN ' . $this->_tableViews . ' AS v ON v.tid = t.id';

        $filter = (array)$filter;

        $order = ' ORDER BY ';

        if ($filter) {
          if (!empty($filter['f_next'])) {
            $order .= ' date_start ASC ';

          }
          /*elseif (!empty($filter['f_prev'])) {

            $order .= ' date_start DESC ';
          }*/
          else {

            $order .= ' date_start DESC ';
          }
        } else {
          $order .= ' date_start DESC ';
        }

        $filter['f_access'] = $checkAccess && !User::getInstance()->isAdmin();

        $sql .= $this->_where($filter);
        $sql .= $order;

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;
        }

        //echo $sql;

        $result = $pdo->query($sql);

        $coll = array();

        foreach ($result as $row) {
            $coll[] = new \VideoAdmin\Model\Translation\Item($row);
        }

        return $coll;
    }

    protected function _where(array $filter = null) {

      $sql = array();

      if ($filter) {
        if (!empty($filter['f_parent'])) {
          $sql[] = ' parent_id = 0 ';
        }

        if (!empty($filter['f_live'])) {
          $sql[] = ' t.media_state = ' . self::STATE_LIVE;
        }

        if (!empty($filter['f_next'])) {
          $sql[] = ' (t.date_start > UNIX_TIMESTAMP(NOW()) OR t.media_state = ' . self::STATE_LIVE . ') ';
        }

        if (!empty($filter['f_prev'])) {
          $sql[] = ' t.date_start < UNIX_TIMESTAMP(NOW()) ';
        }

        if (!empty($filter['f_access'])) {
          $sql[] = ' t.user_name = ' . \Registry::get('PDO')->quote( User::getInstance()->getLogin() );
        }

        if (isset($filter['f_cdn'])) {
          if (true == $filter['f_cdn']) {
            $sql[] = ' t.cdn = 1 ';
          } else {
            $sql[] = ' t.cdn = 0 ';
          }
        }

        if (isset($filter['f_announce'])) {
          if (true == $filter['f_announce']) {
            $sql[] = ' t.media_state = ' . self::STATE_ANNOUNCE . ' ';
          } else {
            $sql[] = ' (t.media_state = ' . self::STATE_LIVE . ' OR t.media_state = ' . self::STATE_ARCHIVE .') ';
          }
        }

      }

      return $sql ? (' WHERE ' . implode(' AND ', $sql)) : null;
    }

    /**
     * @param $id
     * @param bool $updateCache
     * @throws \ErrorException
     * @return \VideoAdmin\Model\Translation\Item
     */
    public function getItem($id, $updateCache = false) {

        if (!$updateCache) {
            $item = $this->getItemFromCache($id);
        } else {
            $item = null;
        }

        if (!$item) {
            $sql = 'SELECT t.*, ln.name AS line, tl.log_id AS logsheet_id FROM ' . $this->_table . ' AS t
                      LEFT JOIN ' . $this->_tableLines .' AS ln ON t.line_id = ln.id
                      LEFT JOIN ' . $this->_tableLogSheet . ' AS tl ON t.id = tl.translation_id
                WHERE t.id = ' . (int)$id;

            /** @var \PDO $pdo  */
            $pdo = \Registry::get('PDO');

            $result = array();
            $rows = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                if (!$result) {
                  $result = $row;

                  if ($row['logsheet_id']) {
                    $result['logsheet_id'] = array($row['logsheet_id']);
                  } else {
                    $result['logsheet_id'] = array();
                  }
                } else {
                  $result['logsheet_id'][] = $row['logsheet_id'];
                }
            }

            if ($result) {
                $item = new \VideoAdmin\Model\Translation\Item( $result );
                $this->addItemToCache($item);
            } else {
                throw new \ErrorException('Empty result');
            }
        }

        return $item;
    }

    public function getNodeId($translationId) {
        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $result = $pdo->query('SELECT `nid` FROM translations2nodes WHERE `tid` = ' . (int)$translationId);

        if ($result) {
            return $result->fetchColumn();
        }

        return null;
    }

    public function getItemByNodeId($nodeId) {
        $nodeId = (int)$nodeId;

        /** @var \VideoAdmin\Model\Memcache $cache */
        $cache = \Registry::get('CACHE');
        $key = "videoadmin:nid-id:{$nodeId}";
        $id = $cache->get($key);

        if (!$id) {
            /** @var \PDO $pdo  */
            $pdo = \Registry::get('PDO');
            $id = $pdo->query("SELECT `tid` FROM translations2nodes WHERE `nid` = {$nodeId}")->fetchColumn();

            if ($id) {
                $cache->set($key, $id, 0);
            }
        }

        if ($id > 0) {
            return $this->getItem($id);
        }

        return null;
    }

    public function updateStats($pp, $count) {
      /** @var \PDO $pdo  */
      $pdo = \Registry::get('PDO');

      if ($count > 0) {
        $count = (int)$count;

        $sth = $pdo->prepare("UPDATE {$this->_table} SET online = ? WHERE play_point = ?");
        $sth->execute(array($count, $pp));

        $sth = $pdo->prepare("UPDATE {$this->_table} SET max_views = if ($count > max_views, $count, max_views) WHERE play_point = ?");
        $sth->execute(array($pp));
      }
    }

    public function resetStats() {
      /** @var \PDO $pdo  */
      $pdo = \Registry::get('PDO');
      $pdo->query("UPDATE {$this->_table} SET online = 0 WHERE online > 0");
    }

    public function countTotal($checkAccess = true, array $filter = null) {
        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');

        $sql = "SELECT COUNT(*) FROM {$this->_table} AS t";

        $filter = (array)$filter;
        $filter['f_access'] = $checkAccess && !User::getInstance()->isAdmin();

        $sql .= $this->_where($filter);

        $result = $pdo->query($sql)->fetchColumn();

        return $result;
    }

    public function changeState($id, $status) {
        $status = (int)$status;

        if (!in_array($status, array(self::STATE_ANNOUNCE, self::STATE_LIVE, self::STATE_ARCHIVE))) {
            throw new \InvalidArgumentException('Status is wrong');
        }

        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');

        /** @var \PDOStatement $stmt */
        $stmt = $pdo->prepare('UPDATE ' . $this->_table . ' SET media_state = ? WHERE id = ?');
        $result = $stmt->execute(array($status, $id));

        if ($result) {
            $updatedItem = $this->getItem($id, true);
            $this->notify($updatedItem);
        }

        return $result;
    }

    public function start($id) {
        $item = $this->getItem($id);

        if ($item) {
            $pp = $item['play_point'];
            $line = $item['line'];

            $cmd = null;
            $version = null;

            if ($item['dvr']) {
              if ($item['cdn']) {
                $cmd = \VideoAdmin\Wowza\Server::CMD_START_DVR_CDN;
              } else {
                $cmd = \VideoAdmin\Wowza\Server::CMD_START_DVR;
              }
              $version = \VideoAdmin\Wowza\Server::VERSION;
            }

            $server = new \VideoAdmin\Wowza\Server();
            $result = $server->start($pp, $line, $version, $cmd);

            if ($result) {
              /** @var \PDO $pdo  */
              $pdo = \Registry::get('PDO');

              /** @var \PDOStatement $stmt */
              $stmt = $pdo->prepare('UPDATE ' . $this->_table . ' SET start_live_time = ?, stop_live_time = null WHERE id = ?');
              $stmt->execute(array(time(), $id));
            }

            return $result;
        }

        return null;
    }

    public function stop($id) {
        $item = $this->getItem($id);

        if ($item) {
            $pp = $item['play_point'];

            $cmd = null;

            if ($item['dvr']) {
              if ($item['cdn']) {
                $cmd = \VideoAdmin\Wowza\Server::CMD_STOP_DVR_CDN;
              } else {
                $cmd = \VideoAdmin\Wowza\Server::CMD_STOP_DVR;
              }
            }

            $server = new \VideoAdmin\Wowza\Server();
            $result = $server->stop($pp, $cmd);

            if ($result > 0) {
              /** @var \PDO $pdo  */
              $pdo = \Registry::get('PDO');

              /** @var \PDOStatement $stmt */
              $stmt = $pdo->prepare('UPDATE ' . $this->_table . ' SET stop_live_time = ? WHERE id = ?');
              $stmt->execute(array(time(), $id));
            }

            return $result;
        }

        return null;
    }

    public function addHit($id) {
        $item = $this->getItem($id);

        if ($item && !$item->isAnnounce()) {
            $tid = (int)$id;
            $live = (int)$item->isLive();
            $archive = (int)$item->isArchive();

            $sql = "INSERT INTO translation_views(tid, views_live, views_archive) VALUES ($tid, $live, $archive)
                ON DUPLICATE KEY UPDATE tid = $tid, views_live = views_live + $live, views_archive = views_archive + $archive";

            /** @var \PDO $pdo  */
            $pdo = \Registry::get('PDO');
            $pdo->query($sql);
        }
    }

    public function getHits($id) {
      /** @var \PDO $pdo  */
      $pdo = \Registry::get('PDO');

      $sth = $pdo->query("SELECT views_live, views_archive FROM translation_views WHERE tid = $id");
      $result = null;

      if ($sth) {
        $result = $sth->fetch(\PDO::FETCH_ASSOC);
      }

      if ($result) {
        return $result;
      } else {
        return array(
          'views_live' => 0,
          'views_archive' => 0
        );
      }
    }

    public function checkAccessRights(Translation\Item $translation, User $user) {
      $login = $user->getLogin();
      $access = false;

      if ($user->isAdmin()) {
        $access = true;
      } elseif ($login == $translation['user_name']) {
        $access = true;
      }

      return $access;
    }

    public function getMarks($id, $time = null) {
      $id = (int)$id;
      $time = (int)$time;

      $item = $this->getItem($id);

      $sql = "
        SELECT l.name, l.time
        FROM logsheet_labels l
        WHERE l.log_id = (
          SELECT log_id FROM translation_logsheets t
          WHERE t.translation_id = {$id} LIMIT 1
        )
      ";

      if ($time > 0) {
        $sql .= " AND l.time > {$time}";
      }

      $sql .= " ORDER BY l.time DESC";

      /** @var \PDO $pdo  */
      $pdo = \Registry::get('PDO');

      $data = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

      $result = array();

      foreach ($data as $row) {
        $seekTime = $row['time'] - $item['start_live_time'];

        $row['display_time'] = date("H:i:s", $row['time']);
        $row['full_time'] = $row['time'];
        $row['time'] = $seekTime;

        $result[] = $row;
      }

      /*echo '<pre>';
      var_dump($result);exit;*/

      return $result;
    }

    public function getMarksFromCache($id, $time = null) {
        /** @var \VideoAdmin\Model\Memcache $cache */
        $cache = \Registry::get('CACHE');
        $key = "videoadmin:marks:{$id}";
        $result = $cache->get($key);

        if (false === $result) {
            //берем все метки трансляции
            $result = $this->getMarks($id);

            //и кладем в кеш
            $item = $this->getItem($id);
            if ($item->isArchive() && $item['stop_live_time'] < time() - 600) {
                $ttl = 0;
            } else {
                $ttl = 45;
            }
            $cache->add($key, serialize($result), $ttl);
        } else {
            $result = unserialize($result);
        }

        //выбираем по времени
        if ($time > 0) {
            foreach ($result as $key => $row) {
                if ($row['full_time'] <= $time) {
                    unset($result[$key]);
                }
            }
        }

        return $result;
    }

    /**
     * @return \SplObjectStorage
     */
    protected function _initStorage() {
        if (null === $this->_storage) {
            $this->_storage = new \SplObjectStorage();
        }
        return $this->_storage;
    }

    protected function newPlayPoint($name, $dateStart) {
        $playPoint = $this->_getPlayPoint($name, $dateStart);

        $pdo = \Registry::get('PDO');
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$this->_table} WHERE play_point = :play_point");

        $pp = $playPoint;
        $i = 0;
        do {
            $stmt->execute(array('play_point' => $pp));
            $result = $stmt->fetchColumn();
            if ($result > 0) {
                $pp = $playPoint.(++$i);
            }
        } while ($result > 0);

        return $pp;
    }

    protected function _getPlayPoint($name, $dateStart) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'y',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => "",    'ы' => 'y',   'ъ' => "",
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            /*'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'Y',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => "",    'Ы' => 'Y',   'Ъ' => "",
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',*/
        );

        $name = strtr( mb_convert_case($name, MB_CASE_LOWER, 'UTF-8'), $converter);
        $name = preg_replace('/[^\p{L}\d]+/u', '', $name);

        $dateStart = date('YmdHi', $dateStart);

        return $dateStart . $name;
    }

    public function getChannelItems($id) {
        $items = array();
        $channels = $this->getChannels($id);
        foreach ($channels as $key => $channel) {
            if (!empty($channel['child_translation_id'])) {
                $items[$key] = $this->getItem($channel['child_translation_id']);
            }
        }
        return $items;
    }

    public function getChannelId($id, $channel) {
        $channels = $this->getChannels($id);
        if (!empty($channels[$channel]['child_translation_id']) && !empty($channels[$channel]['name'])) {
            return (int)$channels[$channel]['child_translation_id'];
        }
    }

    public function getChannels($id) {
        if (!isset(self::$_channels[$id])) {
            $cache = \Registry::get('CACHE');
            $key = "videoadmin:channels:{$id}";
            $channels = $cache->get($key);
            if ($channels === false) {
                $channels = $this->_getChannels($id);
                $cache->set($key, $channels);
            }
            self::$_channels[$id] = $channels;
        }
        return self::$_channels[$id];
    }

    protected function _getChannels($id) {
        $pdo = \Registry::get('PDO');

        $st = $pdo->prepare("
            SELECT ch.*, t.line_id FROM {$this->_tableChannels} ch
            LEFT JOIN {$this->_table} t ON t.id = ch.child_translation_id
            WHERE ch.translation_id = :id
        ");
        $st->execute(array(
            'id' => $id
        ));
        $res = $st->fetchAll(\PDO::FETCH_ASSOC);

        $channels = array();

        if ($res) {
            foreach ($res as $channel) {
                $channels[$channel['channel']] = $channel;
            }
        }

        return $channels;
    }

    public function saveChannels($id, $channels) {
        $pdo = \Registry::get('PDO');

        $existingChannels = $this->getChannels($id);
        $newChannels = array();

        $insertChannel = $pdo->prepare("
            INSERT INTO {$this->_tableChannels}
            (translation_id, channel, child_translation_id, quadro, name)
            VALUES (:translation_id, :channel, :child_translation_id, :quadro, :name)
        ");
        $updateChannel = $pdo->prepare("
            UPDATE {$this->_tableChannels}
            SET quadro = :quadro, name = :name
            WHERE translation_id = :translation_id AND channel = :channel
        ");
        $updateTranslation = $pdo->prepare("
            UPDATE {$this->_table}
            SET line_id = :line_id
            WHERE id = :id
        ");

        foreach ($channels as $key => $channel) {
            if (isset($channel['name'])) {
                if (empty($existingChannels[$key])) {
                    if (!empty($channel['name'])) {
                        // Создать клон трансляции
                        $clone = $this->createClone($id, $key, $channel['name']);
                        $ch = $newChannels[$key] = array(
                            'translation_id' => $id,
                            'channel' => $key,
                            'child_translation_id' => $key == 1 ? 0 : $clone['id'],
                            'quadro' => !empty($channel['quadro']) ? 1 : 0,
                            'name' => $channel['name'],
                        );

                        $insertChannel->execute($newChannels[$key]);
                    }
                } else {
                    $ch = $existingChannels[$key];

                    // Обновить клон трансляции
                    $updateChannel->execute(array(
                        'translation_id' => $id,
                        'channel' => $key,
                        'quadro' => !empty($channel['quadro']) ? 1 : 0,
                        'name' => $channel['name'],
                    ));
                }

                // Обновить line_id
                if (isset($channel['line_id'])) {
                    $updateTranslation->execute(array(
                        'line_id' => $channel['line_id'],
                        'id' => $ch['child_translation_id']
                    ));
                }
            }
        }

        // Сбросить кэш
        unset(self::$_channels[$id]);
        \Registry::get('CACHE')->delete("videoadmin:channels:{$id}");
    }

    public function createClone($id, $key, $channel_name = null) {
        if ($key == 1) {
            return $this->getItem($id);
        }

        $pdo = \Registry::get('PDO');

        $fields = $this->getFieldNames();
        unset($fields[0]);

        $st = $pdo->prepare("SELECT * FROM {$this->_table} WHERE id = ?");
        $st->execute(array($id));
        $data = $st->fetch(\PDO::FETCH_ASSOC);

        if ($channel_name === null) {
            $channel_name = $key;
        }

        unset($data['id']);
        $data['parent_id'] = $id;
        $data['name'] = "{$data['name']} - {$channel_name}";
        $data['play_point'] = $this->newPlayPoint($data['name'], $data['date_start']);
        $data['dvr'] = 0;

        $set = array();
        foreach ($data as $key => $value) {
            $set[] = "`{$key}` = :{$key}";
        }
        $set = implode(",", $set);

        $st = $pdo->prepare("
            INSERT INTO {$this->_table}
            SET {$set}
        ");
        $st->execute($data);

        return $this->getItem($pdo->lastInsertId());
    }

    protected function getFieldNames() {
        $fields = array();
        $pdo = \Registry::get('PDO');
        $st = $pdo->query("SHOW COLUMNS FROM {$this->_table}");
        if ($st) {
            while ($field = $st->fetchColumn()) {
                $fields[] = $field;
            }
        }
        return $fields;
    }
    //getParentId used in videocut
    public function getParentId($id){
        $pdo = \Registry::get('PDO');
        $pdo->query("SET NAMES 'utf8'");    
        $query="SELECT parent_id FROM translations WHERE id=$id";
        $stmt=$pdo->query($query) or die(print_r($dbh->errorInfo()));            
        $out=$stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $out[0]['parent_id'];
        
    }
 }
