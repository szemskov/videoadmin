<?php

namespace VideoAdmin\Wowza;

class Server {

    const CMD_START = 'start2';

    const CMD_STOP = 'stop2';

    const CMD_START_DVR = 'start4';

    const CMD_STOP_DVR = 'stop4';

    const CMD_START_DVR_CDN  = 'start5';

    const CMD_STOP_DVR_CDN = 'stop5';

    const VERSION = '001';

    protected $_log = false;

    public function __construct() {
        $this->_log = \Registry::get('LOG');
    }

    public function start($pp, $line, $version = null, $cmd = null) {
        if (!$version) {
            $version = self::VERSION;
        }

        if (!$cmd) {
            $cmd = self::CMD_START;
        }

        $url = $this->_buildUrl($cmd, $pp, $line, $version);

        if (true == $this->_log) {
            \Logger::write('time: ' . date('Y-m-d H:i:s'), 'wowza');
            \Logger::write("Start wowza: $url", 'wowza');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $error = curl_errno($ch);
        $errorMessage = curl_error($ch);
        $connectTime = (int)curl_getinfo($ch, CURLINFO_CONNECT_TIME);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if (!$error) {
          $logError = $httpCode;
          $result = $this->_parseResponse($response, $httpCode);
        } else {
          $logError = $errorMessage;
          $result = -1;
        }

        $status = '';
        $error = true;

        switch (true) {
          case $result == 1:
            $error = false;
            $status = 'success';
          break;

          case $result === 0:
            $status = 'failed';
          break;

          case $result === null;
            $status = 'undefined';
          break;

          case $result === -1;
            $status = 'error ' . $logError;
          break;
        }

        if (true == $this->_log) {
            \Logger::write("Wowza cmd status: $status", 'wowza');
            \Logger::write("Wowza connection time: $connectTime", 'wowza');
            \Logger::write("Wowza response: $response", 'wowza');
        }

        return (int)!$error;
    }

    public function stop($pp, $cmd = null) {
        if (!$cmd) {
            $cmd = self::CMD_STOP;
        }

        $url = $this->_buildUrl($cmd, $pp);

        if (true == $this->_log) {
            \Logger::write('time: ' . date('Y-m-d H:i:s'), 'wowza');
            \Logger::write("Stop wowza: $url", 'wowza');
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $error = curl_errno($ch);
        $errorMessage = curl_error($ch);
        $connectTime = (int)curl_getinfo($ch, CURLINFO_CONNECT_TIME);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if (!$error) {
          $logError = $httpCode;
          $result = $this->_parseResponse($response, $httpCode);
        } else {
          $logError = $errorMessage;
          $result = -1;
        }

        $status = '';
        $error = true;

        switch (true) {
          case $result == 1:
            $error = false;
            $status = 'success';
            break;

          case $result === 0:
            $status = 'failed';
            break;

          case $result === null;
            $status = 'undefined';
            break;

          case $result === -1;
            $status = 'error "' . $logError . '"';
          break;
        }

        if (true == $this->_log) {
          \Logger::write("Wowza cmd status: $status", 'wowza');
          \Logger::write("Wowza connection time: $connectTime", 'wowza');
          \Logger::write("Wowza response: $response", 'wowza');
        }

        return (int)!$error;
    }

    protected function _parseResponse($response, $responseCode = null) {
      $result = null;

      if ($responseCode != 200) {
        return -1;
      }

      $response = trim($response);

      if (preg_match('/^([\d])(.*)/sm', $response, $m)) {
        if (isset($m[1])) {

           $result = (int)($m[1] == 1 && !empty($m[2]));

        }
      }

      return $result;
    }

    /**
     * Получает ip-адрес вовзы через балансировщик
     *
     * @return string
     */
    public function getServerByBalancer() {
        
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'http://188.127.244.2:1935/loadbalancer');
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $response = curl_exec($ch);
        $responseCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        $host = null;

        if ($responseCode == 200) {
          $response = trim($response);

          if (preg_match('/redirect=(.*)/', $response, $m)) {
            if (false !== ip2long($m[1])) {
              $host = $m[1];
            }
          }
        }

        return $host;
    }

    /**
     * Возвращает рандомный ip-адрес вовзы
     *
     * @return string
     */
    public function getServerIp() {
      $wowza = \Registry::get('WOWZA');
      if (isset($wowza['ip_list']) && is_array($wowza['ip_list'])) {
        $key = array_rand($wowza['ip_list']);
        return $wowza['ip_list'][$key];
      }

      return null;
    }

    public function getStats() {
      $host = "188.127.244.22";
      $uri = "connectioncounts/";
      $username = "mnt";
      $password = "100lica";

      if (!$fp = fsockopen($host,8086, $errno, $errstr, 15)) {
        return false;
      }

      //first do the non-authenticated header so that the server
      //sends back a 401 error containing its nonce and opaque
      $out = "GET /$uri HTTP/1.1\r\n";
      $out .= "Host: $host\r\n";
      $out .= "Connection: Close\r\n\r\n";
      fwrite($fp, $out);

      //read the reply and look for the WWW-Authenticate element
      while (!feof($fp)) {
        $line = fgets($fp, 512);
        if (strpos($line, "WWW-Authenticate:") !== false) {
          $authline = trim(substr($line, 18));
        }
      }
      fclose($fp);

      //split up the WWW-Authenticate string to find digest-realm,nonce and opaque values
      //if qop value is presented as a comma-seperated list (e.g auth,auth-int) then it won't be retrieved correctly
      //but that doesn't matter because going to use 'auth' anyway
      $authlinearr = explode(",", $authline);
      $autharr = array();
      foreach ($authlinearr as $el) {
        $elarr = explode("=", $el);
        //the substr here is used to remove the double quotes from the values
        $autharr[trim($elarr[0])] = substr($elarr[1], 1, strlen($elarr[1]) - 2);
      }

      //these are all the vals required from the server
      $nonce = $autharr['nonce'];
      //$opaque=$autharr['opaque'];opaque=\"$opaque\",
      $drealm = $autharr['Digest realm'];

      //client nonce can be anything since this authentication session is not going to be persistent
      //likewise for the cookie - just call it MyCookie
      $cnonce = "sausages";

      //calculate the hashes of A1 and A2 as described in RFC 2617
      $a1 = "$username:$drealm:$password";
      $a2 = "GET:/$uri";
      $ha1 = md5($a1);
      $ha2 = md5($a2);

      //calculate the response hash as described in RFC 2617
      $concat = $ha1.':'.$nonce.':00000001:'.$cnonce.':auth:'.$ha2;
      $response=md5($concat);

      //put together the Authorization Request Header
      $out = "GET /$uri HTTP/1.1\r\n";
      $out .= "Host: $host\r\n";
      $out .= "Connection: Close\r\n";
      $out .= "Cookie: cookie=MyCookie\r\n";
      $out .= "Authorization: Digest username=\"$username\", realm=\"$drealm\", qop=\"auth\", algorithm=\"MD5\", uri=\"/$uri\", nonce=\"$nonce\", nc=00000001, cnonce=\"$cnonce\", response=\"$response\"\r\n\r\n";

      if (!$fp = fsockopen($host, 8086, $errno, $errstr, 15)) {
        return false;
      }
      fwrite($fp, $out);

      //read in a string which is the contents of the required file
      $str = null;
      while (!feof($fp)) {
        $str .= fgets($fp, 512);
      }
      fclose($fp);

      $GrabStart = '<WowzaMediaServer>';
      $GrabEnd = '</WowzaMediaServer>';
      $GrabData = preg_match("#$GrabStart(.*)$GrabEnd#", $str, $DataGrabed);
      $xml_data = "<?xml version=\"1.0\"?><WowzaMediaServer>" . $DataGrabed[1] . "</WowzaMediaServer>";
      $sxml = new \SimpleXMLElement($xml_data);
      
      $ret=array();
      foreach($sxml->VHost[0]->Application as $v){
        if ($v->Name=="edge" || $v->Name=="dvredge" || $v->Name=="aT8thoozee" || $v->Name=="aT8thoozeedvr")
            $regexp="/(\d{12}[a-zA-Z0-9]+)_\d{1}/";
        if ($v->Name=="vod" || $v->Name=="un4Aewo7ut")
            $regexp="/(\d{12}[a-zA-Z0-9]+)_.+\.mp4/";
        foreach ($v->ApplicationInstance as $val){
          foreach ($val->Stream as $pp){
              $src=$pp->Name;                
              if (!preg_match($regexp, $src, $matches)) {
                  echo 'Video name invalid format: ';
                      }
                      else {                        
                          if (!isset($ret[$matches[1]])) {
                              $ret[$matches[1]] = (int)$pp->SessionsTotal;
                          } else {
                              $ret[$matches[1]] += (int)$pp->SessionsTotal;
                          }                
                      }     
                  }
        }
      }
      return $ret;

      /*$sd = $play_point . '_1';
      $hd = $play_point . '_2';
      $smil = $play_point . '_001.smil';

      $res_sd = $sxml->xpath("//Application//Name[text()='$app']//parent::Application//Stream/Name[contains(text(),'$sd')]/ancestor::Stream");
      $res_hd = $sxml->xpath("//Application//Name[text()='$app']//parent::Application//Stream/Name[contains(text(),'$hd')]/ancestor::Stream");
      $res_smil = $sxml->xpath("//Application//Name[text()='$app']//parent::Application//Stream/Name[contains(text(),'$smil')]/ancestor::Stream");

      $sum = 0;

      if ($res_hd[0]) {
        $sum += (int)$res_hd[0]->SessionsTotal;
      }

      if ($res_sd[0]) {
        $sum += (int)$res_sd[0]->SessionsTotal;
      }
      
      if ($res_smil[0]) {
        $sum += (int)$res_smil[0]->SessionsTotal;
      }

      return $sum;*/
    }

    protected function getConnectionCounts() {
        $cacheKey = 'Wowza.Server.connectioncounts';
        $str = \Registry::get($cacheKey);
        if (!empty($str)) {
            return $str;
        }

        $host = "188.127.244.22";
        $uri = "connectioncounts/";
        $username = "mnt";
        $password = "100lica";

        if (!$fp = fsockopen($host,8086, $errno, $errstr, 15)) {
            return false;
        }

        //first do the non-authenticated header so that the server
        //sends back a 401 error containing its nonce and opaque
        $out = "GET /$uri HTTP/1.1\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n\r\n";
        fwrite($fp, $out);

        //read the reply and look for the WWW-Authenticate element
        while (!feof($fp)) {
            $line = fgets($fp, 512);
            if (strpos($line, "WWW-Authenticate:") !== false) {
                $authline = trim(substr($line, 18));
            }
        }
        fclose($fp);

        //split up the WWW-Authenticate string to find digest-realm,nonce and opaque values
        //if qop value is presented as a comma-seperated list (e.g auth,auth-int) then it won't be retrieved correctly
        //but that doesn't matter because going to use 'auth' anyway
        $authlinearr = explode(",", $authline);
        $autharr = array();
        foreach ($authlinearr as $el) {
            $elarr = explode("=", $el);
            //the substr here is used to remove the double quotes from the values
            $autharr[trim($elarr[0])] = substr($elarr[1], 1, strlen($elarr[1]) - 2);
        }

        //these are all the vals required from the server
        $nonce = $autharr['nonce'];
        //$opaque=$autharr['opaque'];opaque=\"$opaque\",
        $drealm = $autharr['Digest realm'];

        //client nonce can be anything since this authentication session is not going to be persistent
        //likewise for the cookie - just call it MyCookie
        $cnonce = "sausages";

        //calculate the hashes of A1 and A2 as described in RFC 2617
        $a1 = "$username:$drealm:$password";
        $a2 = "GET:/$uri";
        $ha1 = md5($a1);
        $ha2 = md5($a2);

        //calculate the response hash as described in RFC 2617
        $concat = $ha1.':'.$nonce.':00000001:'.$cnonce.':auth:'.$ha2;
        $response=md5($concat);

        //put together the Authorization Request Header
        $out = "GET /$uri HTTP/1.1\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Cookie: cookie=MyCookie\r\n";
        $out .= "Authorization: Digest username=\"$username\", realm=\"$drealm\", qop=\"auth\", algorithm=\"MD5\", uri=\"/$uri\", nonce=\"$nonce\", nc=00000001, cnonce=\"$cnonce\", response=\"$response\"\r\n\r\n";

        if (!$fp = fsockopen($host, 8086, $errno, $errstr, 15)) {
            return false;
        }
        fwrite($fp, $out);

        //read in a string which is the contents of the required file
        $str = null;
        while (!feof($fp)) {
            $str .= fgets($fp, 512);
        }
        fclose($fp);

        \Registry::set($cacheKey, $str);
        return $str;
    }

    protected function _buildUrl() {
        $args = func_get_args();
        return \Registry::get('WOWZA_URL') . '/' . \Registry::get('WOWZA_PASSWORD') . '/' . implode('/', $args);
    }

}
