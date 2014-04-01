<?php

namespace VideoAdmin\Model\Translation;

class PlayerFactory {

    const DEFAULT_PLAYER = 'jw';
    //const DEFAULT_PLAYER = 'flow';

    public static function getPlayer(\VideoAdmin\Model\Translation\Item $translation, $playerType = null) {

        if (null === $playerType) {
          if ($translation['setevizor'] && $translation->isLive()) {
            $playerType = 'setevizor';

          } elseif ($translation['dvr'] && $translation->isLive() || $translation->isQuadro()) {
            $playerType = 'flow';

          } else {

            $playerType = self::DEFAULT_PLAYER;
          }
        }

        $player = null;

        //@todo переделать фабрику под разные плееры
        if ($playerType == 'jw') {
          switch ($translation['media_state']) {
            //плеер для live-трансляций
            case \VideoAdmin\Model\Translation::STATE_LIVE :
              $player = new \VideoAdmin\Model\Translation\JwPlayer\Live($translation);
              //if ($translation['cdn'])
              //    $player->disableSign();
              break;

            //плеер для архивов
            case \VideoAdmin\Model\Translation::STATE_ARCHIVE :
              $player = new \VideoAdmin\Model\Translation\JwPlayer\Archive($translation);
              break;
          }
        } elseif ($playerType == 'flow') {
          switch ($translation['media_state']) {
            //плеер для live-трансляций
            case \VideoAdmin\Model\Translation::STATE_LIVE :
              $player = new \VideoAdmin\Model\Translation\FlowPlayer\Live($translation);
              //if ($translation['cdn'])
              //    $player->disableSign();
              break;

            //плеер для архивов
            case \VideoAdmin\Model\Translation::STATE_ARCHIVE :
              $player = new \VideoAdmin\Model\Translation\FlowPlayer\Archive($translation);
              break;
          }
        } elseif ($playerType == 'setevizor') {
          $player = new \VideoAdmin\Model\Translation\FlowPlayer\Setevizor($translation);
        }

        return $player;
    }

}