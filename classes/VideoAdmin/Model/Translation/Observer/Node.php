<?php

namespace VideoAdmin\Model\Translation\Observer;

/**
 * Отвечает за обновление ноды трансляции на russiasport.ru
 */
class Node implements \VideoAdmin\Observer\ObserverInterface {

    /** Опубликованная нода */
    const STATUS_PUBLISHED = 1;

    /** Неопубликованная нода */
    const STATUS_UNPUBLISHED = 0;

    /** Соль для создания ключа */
    const SIGN_PASSWORD = 'videofromevs';

    protected $_operation = null;

    public static $dbTable = 'translations2nodes';

    protected static $_operations = array('add', 'edit', 'delete');

    public function __construct($operation) {
        $this->setOperation($operation);
    }

    public function setOperation($operation) {
        if ($this->_checkOperation($operation)) {
            $this->_operation = (string)$operation;
        }
        return $this;
    }

    /**
     * Обновляет ноду
     *
     * @param \VideoAdmin\Model\ItemAbstract $item
     */
    public function update(\VideoAdmin\Model\ItemAbstract $item) {
        if ($this->_operation) {
            $this->{"_" . $this->_operation}($item);
        }
    }

    /**
     * Создание новой ноды
     *
     * @param \VideoAdmin\Model\Translation\Item $item
     */
    protected function _add(\VideoAdmin\Model\Translation\Item $item) {
        $data = $this->_createData($item);

        //создаем неопубликованную ноду
        $data['status'] = self::STATUS_UNPUBLISHED;
        $data['action'] = 0;

        if ($item['node_id'] > 0) {
          $data['action'] = $item['node_id'];

          //при редактировании не передаем заголовок
          unset($data['title']);
        }

        $json = json_encode($data);
        $nodeId = $this->_send($json);

        if ($nodeId > 0) {
            $this->_insertNode($nodeId, $item->getId());
        }
    }

    /**
     * Редактирование ноды
     *
     * @param \VideoAdmin\Model\Translation\Item $item
     */
    protected function _edit(\VideoAdmin\Model\Translation\Item $item) {
        if ($item['node_id'] > 0) {
          $nodeId = $item['node_id'];
        } else {
          $nodeId = $this->_getNodeId($item->getId());
        }

        if ($nodeId > 0) {
            $data = $this->_createData($item);
            $data['action'] = $nodeId;

            //при редактировании не передаем заголовок
            unset($data['title']);

            $json = json_encode($data);
            $result = $this->_send($json);

            if ($result == $nodeId) {
                $this->_updateNode($item->getId(), $nodeId);
            }
        } else {
            $this->_add($item);
        }
    }

    /**
     * Удаление ноды (выставляет статус ноды в 0)
     *
     * @param \VideoAdmin\Model\Translation\Item $item
     */
    protected function _delete(\VideoAdmin\Model\Translation\Item $item) {
        $nodeId = $this->_getNodeId($item->getId());

        if ($nodeId > 0) {
            $data = $this->_createData($item);

            //сбрасываем публикацию у ноды
            $data['status'] = self::STATUS_UNPUBLISHED;
            $data['action'] = $nodeId;

            $json = json_encode($data);
            $result = $this->_send($json);

            if ($result == $nodeId) {
                $this->_deleteNode($item->getId());
            }
        }
    }

    protected function _checkOperation($operation) {
        return in_array($operation, self::$_operations);
    }

    protected function _createData(\VideoAdmin\Model\Translation\Item $item) {

      //var_dump($item['dvr']); exit;

        //был косяк при добавлении заголовков содержащих точки
        $title = str_replace('.', ' ', $item['name']);

        $data = array(
            'translation_id'    => $item->getId(),
            'title'             => $title,
            'live'              => $item->getLiveStatus(),
            'start_live_time'   => $item->getStartLiveTime(),
            'group'             => $item->getGroupId(), //ID группы "трансляции",
            'translation_dvr'   => $item['dvr']
        );

        $key = time();
        $sign = md5(self::SIGN_PASSWORD . ':' . $key);

        $data['sign'] = $sign;
        $data['t'] = $key;

        //файлы для теста
        $test = array(
            //'thumbnail' =>'C:\open_server\domains\videoadmin\test\pic1.jpg',
            'thumbnail' =>'http://s.russiasport.ru/sites/default/files/styles/225x225/public/i/g.png',
        );

        return array_merge($data, $test);
    }

    protected function _send($data) {
        $config = \Registry::get('API');

        $host = $config['host'];
        $path = $config['path'];
        $url = $host . $path;

        $fields = array(
            'data' => $data
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);

        //execute post
        $result = curl_exec($ch);
        $responseCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (\Registry::get('LOG')) {
            \Logger::write('request: ' . $url, 'api');
            \Logger::write('data: ' . var_export($data, true), 'api');
            \Logger::write('query: ' . http_build_query($fields), 'api');
            \Logger::write('code: ' . $responseCode, 'api');
        }

        //close connection
        curl_close($ch);

        if ($responseCode == 200) {
            return (int)$result;
        }

        return null;
    }

    protected function _insertNode($nodeId, $translationId) {
        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $stmt = $pdo->prepare('INSERT INTO ' . self::$dbTable . '(`tid`, `nid`) VALUES(?, ?) ');
        $stmt->execute(array($translationId, $nodeId));
    }

    protected function _updateNode($translationId, $nodeId) {
        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $stmt = $pdo->prepare('UPDATE ' . self::$dbTable . ' SET `nid` = ? WHERE `tid` = ?');
        $stmt->execute(array($nodeId, $translationId));
    }

    protected function _deleteNode($translationId) {
        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $stmt = $pdo->prepare('DELETE FROM ' . self::$dbTable . ' WHERE `tid` = ?');
        $stmt->execute(array($translationId));
    }

    protected function _getNodeId($translationId) {
        /** @var \PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $result = $pdo->query('SELECT `nid` FROM ' . self::$dbTable . ' WHERE `tid` = ' . (int)$translationId);
        $id = (int)$result->fetchColumn();

        return $id > 0 ? $id : null;
    }

}