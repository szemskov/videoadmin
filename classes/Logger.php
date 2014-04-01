<?php

class Logger {

    public static $path = './logs';

    public static function write($msg, $type) {

        if (\Registry::exists('LOG_PATH')) {
          $path = \Registry::get('LOG_PATH');
        } else {
          $path = self::$path;
        }

        $file = $path . DIRECTORY_SEPARATOR . $type . '.log';
        $msg .= "\r\n===========================\r\n";
        file_put_contents($file, $msg, FILE_APPEND);
    }

}