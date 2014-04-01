<?php

namespace VideoAdmin\Model;

class User extends ModelAbstract {

  private static $_instance = null;

  public static function getInstance() {
    if (null === self::$_instance) {
      self::$_instance = new self();
    }
    return self::$_instance;
  }

  public function getLogin() {
    $login = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;

    if (empty($login)) {
      $login = \Registry::get('USER');
    }

    return $login;
  }

  public function getRole() {
    /** @var \PDO $pdo */
    $pdo = \Registry::get('PDO');
    $stmt = $pdo->prepare("SELECT `role` from users WHERE `name` = ?");

    $result = $stmt->execute(array($this->getLogin()));

    $role = null;

    if ($result) {
      $role = $stmt->fetchColumn();
    }

    return $role;
  }

  public function isAdmin() {
    return $this->getRole() == 'admin';
  }

}