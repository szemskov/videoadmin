<?php

namespace VideoAdmin\Controller;

class Videocut extends ControllerAbstract {
    
   public function index(){
        
        $tid = (int)$_GET['id'];        
        $model = new \VideoAdmin\Model\Videocut($tid);
        echo $model->videocutGetForm();
    }
    
}
?>
