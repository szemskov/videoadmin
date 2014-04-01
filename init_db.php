<?php

$pdoDb = DB::createFromConfig($config['DB']);
$pdoDb->query('SET NAMES utf8');

Registry::set('PDO', $pdoDb);
