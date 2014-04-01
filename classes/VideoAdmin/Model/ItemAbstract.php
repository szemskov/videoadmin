<?php

namespace VideoAdmin\Model;

class ItemAbstract implements \ArrayAccess {

    protected $_id = null;

    protected $_model = null;

    protected $_data = array();

    public function __construct(array $row) {
        if (isset($row['id'])) {
            $this->_id = (int)$row['id'];
        } else {
            throw new \InvalidArgumentException('ID field not found');
        }
        $this->_data = $row;
    }

    public function getId() {
        return $this->_id;
    }

    public function getModel() {
        if (null === $this->_model) {
            $parts = explode('\\', get_called_class());
            array_pop($parts);
            $class = implode('\\', $parts);

            $this->_model = new $class;
        }
        return $this->_model;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return $this->_data[$offset];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->_data[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    public function __toString() {
        return $this['id'];
    }

    public function toArray() {
        return $this->_data;
    }
}