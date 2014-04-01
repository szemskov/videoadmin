<?php

namespace VideoAdmin\Model;

class Blacklist extends ModelAbstract {

    protected $_table = 'ip_blacklist';

    public function add($name, $keywords = null) {
        $keywords = !empty($keywords) ? $keywords : null;

        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');

        /** @var \PDOStatement $stmt */
        $stmt = $pdo->prepare('INSERT INTO ' . $this->_table . '(name, iplist) VALUES(?, ?)');
        $result = $stmt->execute(array($name, $keywords));

        return $result;
    }

    public function edit($id, $name, $keywords = null) {
        $stream = !empty($keywords) ? $keywords : null;

        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');

        /** @var \PDOStatement $stmt */
        $stmt = $pdo->prepare('UPDATE ' . $this->_table . ' SET name = ?, iplist = ? WHERE id = ?');
        $result = $stmt->execute(array($name, $keywords, $id));

        return $result;
    }

    public function delete($id) {
        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $result = $pdo->query('DELETE FROM ' . $this->_table . ' WHERE id= ' . (int)$id);

        return $result->rowCount();
    }

    public function getItems($sortBy = null, $sortOrder = null, $limit = null, $offset = null) {
        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');

        $sortBy = !empty($sortBy) ? "`$sortBy`" : 'id';
        $sortOrder = strtolower($sortOrder) == 'asc' ? 'ASC' : 'DESC';

        $sql = 'SELECT * FROM ' . $this->_table . ' ORDER BY ' .$sortBy. ' ' .$sortOrder;

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;
        }

        $result = $pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

        $coll = array();

        foreach ($result as $row) {
            $coll[] = new \VideoAdmin\Model\Blacklist\Item($row);
        }

        return $coll;
    }

    public function getItem($id) {
        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $result = $pdo->query('SELECT * FROM ' . $this->_table . ' WHERE id = ' . (int)$id);

        return new \VideoAdmin\Model\Blacklist\Item( $result->fetch() );
    }

    public function countTotal() {
        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $result = $pdo->query('SELECT COUNT(*) FROM ' . $this->_table);

        return $result->fetchColumn();
    }

    public static function checkIp($clientIp) {

      $model = new self();

      $list = $model->getItems();

      foreach ($list as $item) {

        //проверка подсети
        $ar = explode("\r\n", $item['iplist']);

        foreach ($ar as $ip) {
          if ($ip == $clientIp) {
            return false;

          } elseif (false !== strpos($ip, '*')) {
            $mask = str_replace('.*', '', $ip);

            if (0 === strpos($clientIp, $mask)) {
              return false;
            }
          } elseif (false !== strpos($ip, '/')) {

            list($net, $mask) = explode("/", $ip);

            $ip_net = ip2long($net);
            $ip_mask = ~((1 << (32 - $mask)) - 1);

            $ip_ip = ip2long($clientIp);

            $ip_ip_net = $ip_ip & $ip_mask;

            if ($ip_ip_net == $ip_net) {
              return false;
            }
          }
        }

      }

      return true;

    }

}