<?php

class Network {

    static public function isPrivateNetwork($ip) {
        if (preg_match("/unknown/", $ip))
            return true;
        if (preg_match("/127\.0\./", $ip))
            return true;
        if (preg_match("/^192\.168\./", $ip))
            return true;
        if (preg_match("/^10\./", $ip))
            return true;
        if (preg_match("/^172\.16\./", $ip))
            return true;
        if (preg_match("/^172\.17\./", $ip))
            return true;
        if (preg_match("/^172\.18\./", $ip))
            return true;
        if (preg_match("/^172\.19\./", $ip))
            return true;
        if (preg_match("/^172\.20\./", $ip))
            return true;
        if (preg_match("/^172\.21\./", $ip))
            return true;
        if (preg_match("/^172\.22\./", $ip))
            return true;
        if (preg_match("/^172\.23\./", $ip))
            return true;
        if (preg_match("/^172\.24\./", $ip))
            return true;
        if (preg_match("/^172\.25\./", $ip))
            return true;
        if (preg_match("/^172\.26\./", $ip))
            return true;
        if (preg_match("/^172\.27\./", $ip))
            return true;
        if (preg_match("/^172\.28\./", $ip))
            return true;
        if (preg_match("/^172\.29\./", $ip))
            return true;
        if (preg_match("/^172\.30\./", $ip))
            return true;
        if (preg_match("/^172\.31\./", $ip))
            return true;

        return false;
    }

    static public function getClientIp() {
        //$ip = $_SERVER['REMOTE_ADDR'];
        $remote_addr = $_SERVER['REMOTE_ADDR'];

        if (array_key_exists("HTTP_X_FORWARDED_FOR", $_SERVER) && $_SERVER['HTTP_X_FORWARDED_FOR']) {

            $list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

            // TODO: move private network determination to helper
            foreach ($list as $ip) {
                if (\Network::isPrivateNetwork($ip))
                    break;

                $remote_addr = $ip;
            }
        }

        // TODO: move substitutions to helper, make cleaner logic
        // substitute ip for local network (shabolovka)
        
        if (preg_match("/127\.0\./", $remote_addr)) {
            //$remote_addr = "85.174.190.18";
            $remote_addr = "85.174.236.130";
        }

        if (preg_match("/192\.168\./", $remote_addr)) {
            //$remote_addr = "80.247.45.128";
            $remote_addr = "178.34.142.202";
        }
        
        if (preg_match("/10\.208\./", $remote_addr)) {
                //$remote_addr = "80.247.45.128";
                $remote_addr = "178.34.142.202";
        }
        
        if (preg_match("/10\.209\./", $remote_addr)) {
                //$remote_addr = "80.247.45.128";
                $remote_addr = "178.34.142.202";
        }

        return $remote_addr;
    }

    static public function getCountryCode($remote_addr = '') {
      if (empty($remote_addr)) {
        $remote_addr = $_SERVER['REMOTE_ADDR'];
      }

      $country_code = geoip_country_code_by_name($remote_addr);

      if (preg_match("/^192\.168\./", $remote_addr) || preg_match("/^10\./", $remote_addr) || preg_match("/^127\./", $remote_addr))
      {
        $country_code = 'RU';
      }

      return $country_code;
    }

}