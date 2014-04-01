<?php

namespace VideoAdmin\Observer;

interface ObserverInterface {

    function update(\VideoAdmin\Model\ItemAbstract $item);

}