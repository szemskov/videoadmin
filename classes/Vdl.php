<?php

class Vdl {

    /**
     * Prefer functional interfaces.
     * @return int
     */
    static public function getQualityNum() {
        $quality_num = $_GET['playeri'];

        if ($quality_num == '')
            $quality_num = 1; // first content item

        return $quality_num;
    }

    // TODO: obsolete, cleanup usages and remove
    static public function isPrivateNetwork($ip) {
        return \Network::isPrivateNetwork($ip);
    }

    // TODO: obsolete, cleanup usages and remove
    static public function getClientIpForSign() {
        return \Network::getClientIp();
    }

}
