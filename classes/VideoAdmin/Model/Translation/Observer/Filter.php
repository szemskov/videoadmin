<?php

namespace VideoAdmin\Model\Translation\Observer;

/**
 * Отвечает за обновление ноды трансляции на russiasport.ru
 */
class Filter implements \VideoAdmin\Observer\ObserverInterface {

  public function update(\VideoAdmin\Model\ItemAbstract $item) {
      $item->updateLogSheetLabels();
  }

}