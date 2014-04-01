<?php

class DB extends PDO {

    public static function createFromConfig(array $config) {
        $dsn = "mysql:dbname={$config['name']};host={$config['host']}";
        $username = $config['user'];
        $password = $config['password'];

        return new DB($dsn, $username, $password);
    }

}