<?php

define('ROOT_DIR', dirname(__FILE__));

include_once ROOT_DIR.'/classes/Autoloader.php';
$autoloader = new Autoloader();

include_once ROOT_DIR.'/init.php';
include_once ROOT_DIR.'/init_db.php';

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'translation';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

try {
    if (empty($controller) || empty($action)) {
        throw new Exception('Страница не найдена', 404);
    }

    $class = '\VideoAdmin\Controller\\' . ucfirst($controller);

    $autoloader->call($class, $action);

} catch (Exception $e) {
    header("HTTP/1.0 404 Not Found");
    echo $e->getMessage();
    exit;
}
