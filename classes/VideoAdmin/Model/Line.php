<?php

namespace VideoAdmin\Model;

class Line extends ModelAbstract {

    protected $_table = 'play_lines';

    public function add($name, $stream = null) {
        $name = $this->_prepareName($name);
        $stream = !empty($stream) ? $stream : null;

        /** @var PDO $pdo  */
        $pdo = \Registry::get('PDO');

        /** @var PDOStatement $stmt */
        $stmt = $pdo->prepare('INSERT INTO ' . $this->_table . '(name, stream) VALUES(?, ?)');
        $result = $stmt->execute(array($name, $stream));

        return $result;
    }

    public function edit($id, $name, $stream = null) {
        $name = $this->_prepareName($name);
        $stream = !empty($stream) ? $stream : null;

        /** @var PDO $pdo  */
        $pdo = \Registry::get('PDO');

        /** @var PDOStatement $stmt */
        $stmt = $pdo->prepare('UPDATE ' . $this->_table . ' SET name = ?, stream = ? WHERE id = ?');
        $result = $stmt->execute(array($name, $stream, $id));

        return $result;
    }

    public function delete($id) {
        /** @var PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $result = $pdo->query('DELETE FROM ' . $this->_table . ' WHERE id= ' . (int)$id);

        return $result->rowCount();
    }

    public function getItems($sortBy = null, $sortOrder = null, $limit = null, $offset = null) {

        $sql = 'SELECT * FROM ' . $this->_table . ' ORDER BY id DESC';

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int)$limit . ' OFFSET ' . (int)$offset;
        }

        /** @var PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $result = $pdo->query($sql);

        $coll = array();

        foreach ($result as $row) {
            $coll[] = new \VideoAdmin\Model\Line\Item($row);
        }

        return $coll;
    }

    public function getItem($id) {
        /** @var PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $result = $pdo->query('SELECT * FROM ' . $this->_table . ' WHERE id = ' . (int)$id);

        return new \VideoAdmin\Model\Line\Item( $result->fetch() );
    }

    public function countTotal() {
        /** @var PDO $pdo  */
        $pdo = \Registry::get('PDO');
        $result = $pdo->query('SELECT COUNT(*) FROM ' . $this->_table);

        return $result->fetchColumn();
    }

    protected function _prepareName($name) {
        return preg_replace('/[^\p{L}\d]+/u' , '', $name);
    }

}