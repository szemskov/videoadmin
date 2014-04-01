<?php

namespace VideoAdmin\Model\Translation;

class Item extends \VideoAdmin\Model\ItemAbstract {

    /** ID Группы "Трансляции" */
    const GROUP_ID = 226;

    public function changeState($status) {
        $observerNode = new \VideoAdmin\Model\Translation\Observer\Node('edit');

        /** @var \VideoAdmin\Model\Translation $model */
        $model = $this->getModel();
        $model->attach($observerNode);

        $model->changeState($this->_id, $status);
    }

    public function start() {
        /** @var \VideoAdmin\Model\Translation $model */
        $model = $this->getModel();
        $result = $model->start($this->getId());

        //if ($result) {
          $this->changeState(\VideoAdmin\Model\Translation::STATE_LIVE);
        //}

        return $result;
    }

    public function stop() {
        /** @var \VideoAdmin\Model\Translation $model */
        $model = $this->getModel();
        $result = $model->stop($this->getId());

        $this->changeState(\VideoAdmin\Model\Translation::STATE_ARCHIVE);

        return $result;
    }

    public function addHit() {
        /** @var \VideoAdmin\Model\Translation $model */
        $model = $this->getModel();
        $model->addHit($this->getId());
    }

    public function getPublishPoint() {
        return $this['play_point'];
    }

    public function getStartLiveTime() {
        if ($this->isLive()) {
            //return $this['start_live_time'];
            return $this['date_start'];
        } else {
            return $this['date_start'];
        }
    }

    public function getLine() {
        if ($this['line_id'] > 0) {
            $model = new \VideoAdmin\Model\Line();
            return $model->getItem($this['line_id']);
        } else {
            return null;
        }
    }

    /**
     * @deprecated
     * @return string
     */
    public function getKey() {
        return md5('videofromevs: ' . $this['name']);
    }

    public function getGroupId() {
        return \Registry::get('GROUP_ID');
    }

    public function getNodeId() {
        /** @var \VideoAdmin\Model\Translation $model */
        $model = $this->getModel();

        if ($this['node_id'] > 0) {
            return (int)$this['node_id'];
        } else {
            return $model->getNodeId( $this->getId() );
        }
    }

    public function getLiveStatus() {
        return (int)$this->isLive();
    }

    public function getWidget($type, $params = null) {
        $widget = null;

        $params = (array)$params;

        switch ($type) {
            case 'media':
              $widget = new WidgetPlayer($this);
            break;

            case 'widget':
              $widget = new WidgetIframe($this);
            break;
        }

        if ($params) {
          $widget->setOptions($params);
        }

        return $widget;
    }

    public function isLive() {
        return $this['media_state'] == \VideoAdmin\Model\Translation::STATE_LIVE;
    }

    public function isArchive() {
        return $this['media_state'] == \VideoAdmin\Model\Translation::STATE_ARCHIVE;
    }

    public function isAnnounce() {
        return $this['media_state'] == \VideoAdmin\Model\Translation::STATE_ANNOUNCE;
    }

    public function isQuadro() {
        $id = $this['id'];
        $channels = $this->getChannels();
        foreach ($channels as $channel) {
            if ($channel['child_translation_id'] == $id) {
                return !empty($channel['quadro']);
            }
        }
        return false;
    }

    public function getLink() {
        $group = $this->getGroupId();
        $node = $this->getNodeId();
        return "/user/$group/node/$node";
    }

    public function getIframe() {
        $host = \Registry::get('HOST');

        return \Template::show('widget', array(
          'host' => $host,
          'id'   => $this->getId(),
          'width' => 560,
          'height' => 320
        ), true);
    }

    public function setFields(array $fields) {
        /** @var \VideoAdmin\Model\Translation $model */
        $model = $this->getModel();
        $model->setFields($this['id'], $fields);
    }

    public function setLogSheetId($logId) {
        /** @var \VideoAdmin\Model\Translation $model */
        $model = $this->getModel();
        $model->setLogSheetId($this['id'], $logId);
    }

    public function getLogSheetId() {
        /** @var \VideoAdmin\Model\Translation $model */
        $model = $this->getModel();
        return $model->getLogSheetId($this['id']);
    }

    public function getMarks($time = null) {
        /** @var \VideoAdmin\Model\Translation $model */
        $model = $this->getModel();
        return $model->getMarks($this['id'], $time);
    }

    public function getMarksFromCache($time = null) {
      /** @var \VideoAdmin\Model\Translation $model */
      $model = $this->getModel();
      return $model->getMarksFromCache($this['id'], $time);
    }

    public function updateLogSheetLabels() {
      /** @var \VideoAdmin\Model\Translation $model */
      $model = $this->getModel();
      return $model->updateLogSheetLabels($this);
    }

    public function getHits() {
      /** @var \VideoAdmin\Model\Translation $model */
      $model = $this->getModel();
      return $model->getHits($this['id']);
    }

    public function getChannelItems() {
        $model = $this->getModel();
        return $model->getChannelItems($this['id']);
    }

    public function getChannelHits() {
        $hits = array();
        $model = $this->getModel();
        $channels = $this->getChannels();
        foreach ($channels as $key => $channel) {
            if (!empty($channel['child_translation_id'])) {
                $hits[$key] = $model->getHits($channel['child_translation_id']);
            }
        }
        return $hits;
    }

    public function ovaUrlSochi() {
        if (!empty($this->_data['ova_url_sochi'])) {
            $this->_data['ova_url'] = $this->_data['ova_url_sochi'];
        }
        if (!empty($this->_data['ova_url_post_sochi'])) {
            $this->_data['ova_url_post'] = $this->_data['ova_url_post_sochi'];
        }
    }

    public function getChannels() {
        $model = $this->getModel();
        if ($this['parent_id'] > 0) {
            return $model->getChannels($this['parent_id']);
        } else {
            return $model->getChannels($this['id']);
        }
    }
}
