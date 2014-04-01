<?php

define('ROOT_DIR', dirname(__FILE__));

$_SERVER['HTTP_HOST'] = 'videoadmin.russiasport.ru';

include_once __DIR__ . '/classes/Autoloader.php';
$autoloader = new Autoloader();

include_once __DIR__ . '/init.php';

$remote_addr = $_SERVER['REMOTE_ADDR'];

if (array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER) && $_SERVER['HTTP_X_FORWARDED_FOR']) {

	$list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

	// TODO: move private network determination to helper
	foreach ($list as $ip) {
		if (\Network::isPrivateNetwork($ip))
			break;

		$remote_addr = $ip;
                
            if (preg_match("/10\.208\./", $remote_addr)) {
                //$remote_addr = "80.247.45.128";
                $remote_addr = "178.34.142.202";
            }
	}
}

session_start();

if (!isset($_SESSION['ip_list'])) {
	$_SESSION['ip_list'] = array($remote_addr);
} else {
	$_SESSION['ip_list'][] = $remote_addr;
}

echo '<pre>'; print_r($_SESSION['ip_list']); '</pre>';