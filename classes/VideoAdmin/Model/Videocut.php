<?php
namespace VideoAdmin\Model;

class Videocut extends ModelAbstract {
//CREATE TABLE videocut_tasks (id int(11) NOT NULL auto_increment, tid varchar(50) NOT NULL default '', file_360p varchar(200) NOT NULL default '', file_720p varchar(200) NULL default '', ss varchar(50) NULL default '', t varchar(50) NULL default '', state varchar(50) NOT NULL default '' , error int(11) NOT NULL, PRIMARY KEY (id));    
    protected $_table='translations';
    const VIDEOCUT_TABLE='videocut_tasks';    


    //jw player urls
    const PLAYER_JS = "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/jwplayer.js";
    const PLAYER_SKIN = "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/ruskin/ruskin.xml";
    const PLAYER_ = "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/sbplayer.swf";
    const PLAYER_HD = "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/hd.swf";
    const PLAYER_HEADER = "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/Header.swf";
    const PLAYER_RS = "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/RussiaSport.swf";
    const PLAYER_TIMESLIDER = "http://s.russiasport.ru/sites/all/libraries/mediaplayer-5.6/TimeSliderTooltipPlugin.swf";
    //player settings
    const SETTINGS = "{'width':560,'height':340,'stretching':'uniform', 'autostart':false};";
    //server settings
    const WOWZA_URL = "188.127.244.22";    
    const PROTO = "rtmp";
    const APP = "un4Aewo7ut";
        
    protected $tid;
    protected $_360p;
    protected $_720p;
    protected $hd_disabled;
    protected $state;
    
           
    public function __construct($tid) {
        $this->tid=$tid;
    }
    
    static function buildstreamer($proto, $app=null, $file=null){
        return $proto."://".self::WOWZA_URL.":1935/".$app."/".$file;
    }
        
    static function is_empty($arr){
        foreach ($arr as $v){
            if ($v==null){
                $is_empty=true;
            } elseif($v!=null) {
                $is_empty= false;   
                break;
            }            
        }
        return $is_empty;
    } 
    
    public function getTrData(){
                        
            $pdo = \Registry::get('PDO');
            $pdo->query("SET NAMES 'utf8'");
            $stmt = $pdo->prepare("SELECT play_point,hd_disabled FROM $this->_table WHERE id=?");
            $stmt->execute(array($this->tid)) or die(print_r($stmt->errorInfo()));
            $trdata=$stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $this->hd_disabled=$trdata[0]['hd_disabled'];           
            //$task[0]=(string)$this->tid;
            if ($trdata[0]['hd_disabled']==0){
                $this->_360p=$trdata[0]['play_point']."_1_001.mp4";
                $this->_720p=$trdata[0]['play_point']."_2_001.mp4";
            } else {
                $this->_360p=$trdata[0]['play_point']."_001.mp4";
                $this->_720p=null;
            }
            //$task[3]=null;
            //$task[4]=null;
            $this->state='task_init';
            //$task[6]=0;
            //return $task;
            /*unset($stmt);
            $stmt = $pdo->prepare("INSERT INTO ".self::VIDEOCUT_TABLE." (tid,file_360p,file_720p,ss,t,state,error) VALUES(?,?,?,?,?,?,?)");        
            $stmt->execute($task) or die(print_r($stmt->errorInfo()));*/
            return array(
                    $this->_360p,
                    $this->_720p,
                    $this->hd_disabled,
                    $this->state
                    );
    }
    
    public function setVideocutTask($task){
        $pdo = \Registry::get('PDO');
        $pdo->query("SET NAMES 'utf8'");
        $stmt = $pdo->prepare("INSERT INTO ".self::VIDEOCUT_TABLE." (tid,file_360p,file_720p,ss,t,state,error) VALUES(?,?,?,?,?,?,?)");        
        $stmt->execute($task) or die(print_r($stmt->errorInfo()));        
    }
    
    public function getVideocutTask(){
        //Читаем данные из даблицы videocut_tasks если задание в состоянии task_init то перечитываем данные из таблицы translations 
        // и пишем их таблицу videocut_tasks
        $pdo = \Registry::get('PDO');
        $pdo->query("SET NAMES 'utf8'");        
        $stmt = $pdo->prepare("SELECT file_360p,file_720p,ss,t,state,error FROM ".self::VIDEOCUT_TABLE." WHERE tid=?");        
        $stmt->execute(array($this->tid)) or die(print_r($stmt->errorInfo()));
        $data=$stmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($data[0]['state']=='task_init'){
            self::getTrData();
            unset($stmt);
            $stmt = $pdo->prepare("UPDATE ".self::VIDEOCUT_TABLE." SET file_360p=?,file_720p=? WHERE tid=?");        
            $stmt->execute(array($this->_360p,$this->_720p,$this->tid)) or die(print_r($stmt->errorInfo()));
        } else {
            $this->_360p=$data[0]['file_360p'];
            $this->_720p=$data[0]['file_720p'];
            ($data[0]['file_720p']!=null) ? $this->hd_disabled=0 : $this->hd_disabled=1;
            $this->state=$data[0]['state'];        
            return array(
                    $this->_360p,
                    $this->_720p,
                    $this->hd_disabled,
                    $this->state
                    );
        }
        
    }   
    
    public function taskIdExists(){        
        $pdo = \Registry::get('PDO');
        $pdo->query("SET NAMES 'utf8'");
        $stmt = $pdo->prepare("SELECT id FROM ".self::VIDEOCUT_TABLE." WHERE tid=?");        
        $stmt->execute(array($this->tid)) or die(print_r($stmt->errorInfo()));
        $id=$stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (isset($id[0]['id']))
          return true;
        return false;
    }   
    
    public function initVideocutTask(){
        if (!self::taskIdExists()){            
            self::getTrData();
            self::setVideocutTask(array($this->tid,$this->_360p,$this->_720p,null,null,$this->state,0));                
        } else {                            
            self::getVideocutTask();            
        }
    }    

    public function setCutParams($strpos,$endpos){
                
        (!self::is_empty($strpos) and self::is_empty($endpos)) ? $task[0]=$strpos['h'].":".$strpos['m'].":".$strpos['s'] : $task[0]=null;
        (self::is_empty($strpos) and !self::is_empty($endpos)) ? $task[1]=$endpos['h'].":".$endpos['m'].":".$endpos['s'] : $task[1]=null;            
        if (self::is_empty($strpos) and self::is_empty($endpos)){
            $task[0]=$strpos['h'].":".$strpos['m'].":".$strpos['s'];
            $task[1]=$endpos['h'].":".$endpos['m'].":".$endpos['s'];
        }
        $task[2]='params_set';
        $task[3]=$this->tid;
        //write cut params into base        
        $pdo = \Registry::get('PDO');
        $pdo->query("SET NAMES 'utf8'");
        $stmt = $pdo->prepare("UPDATE ".self::VIDEOCUT_TABLE." SET ss=?,t=?,state=? WHERE tid=?");                    
        $stmt->execute($task) or die(print_r($stmt->errorInfo()));
    }
    
    public function CopyandDeleteFiles(){
        $task[0]='copy_del';
        $task[1]=$this->tid;
        $pdo = \Registry::get('PDO');
        $pdo->query("SET NAMES 'utf8'");
        $stmt = $pdo->prepare("UPDATE ".self::VIDEOCUT_TABLE." SET state=? WHERE tid=?");                    
        $stmt->execute($task) or die(print_r($stmt->errorInfo()));
    }
    
    public function remakeCut($_360p,$_720p){
        $task[0]=$_360p;
        $task[1]=$_720p;
        $task[2]='';
        $task[3]='';
        $task[4]='task_init';
        $task[5]=0;
        $task[6]=$this->tid;
        $pdo = \Registry::get('PDO');
        $pdo->query("SET NAMES 'utf8'");
        $stmt = $pdo->prepare("UPDATE ".self::VIDEOCUT_TABLE." SET file_360p=?,file_720p=?,ss=?,t=?,state=?,error=? WHERE tid=?");                    
        $stmt->execute($task) or die(print_r($stmt->errorInfo()));
    }


        public function videocutGetForm(){
        
        self::initVideocutTask();        
        
        if ($this->hd_disabled==0){
            switch ($this->state){
                case "task_init": 
                    return \Template::show("videocut/videocut_hd_1",array('_360p'=>$this->_360p,'_720p'=>$this->_720p,'tid'=>$this->tid));
                    break;
                case "params_set" :
                    return \Template::show("videocut/working",array('tid'=>$this->tid));
                    break;
                case "cut_done" :
                    return \Template::show("videocut/videocut_hd_2",array('_360p'=>$this->_360p,'_720p'=>$this->_720p,'tid'=>$this->tid));
                    break;
                case "copy_del" :
                    return \Template::show("videocut/copy_del",array('tid'=>$this->tid));
                    break;
                case "error" :
                    return \Template::show("videocut/error",array('_360p'=>$this->_360p,'_720p'=>$this->_720p,'tid'=>$this->tid));
                    break; 
            }
            
        } else { 
            switch ($this->state){
                case "task_init": 
                    return \Template::show("videocut/videocut_nohd_1",array('_360p'=>$this->_360p,'tid'=>$this->tid));
                    break;
                case "params_set" :
                    return \Template::show("videocut/working",array('tid'=>$this->tid));
                    break;
                case "cut_done" :
                    return \Template::show("videocut/videocut_nohd_2",array('_360p'=>$this->_360p,'tid'=>$this->tid));
                    break;
                case "copy_del" :
                    return \Template::show("videocut/copy_del",array('tid'=>$this->tid));
                    break;
                case "error" :
                    return \Template::show("videocut/error",array('_360p'=>$this->_360p,'tid'=>$this->tid));
                    break; 
            }                        
        }        
    }
    
}//end of Videocut class
?>
