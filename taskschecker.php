<?php
//this script runs on enc2.russiasport.ru see /etc/crontab.
define("DSN", "mysql:host=db.russiasport.ru;dbname=videoadmin");
define("DB_LOGIN", "videoadmin");
define("DB_PASSWORD", "neegei1uWe6gaet");
//define("DB_NAME", "videoedit");


function runcmd($cmd){        
        $descriptorspec = array(
        0 => array("pipe", "r"),  
        1 => array("pipe", "w"),   
        2 => array("file", "/tmp/error-output.txt", "a") 
        );
        $proc=proc_open(escapeshellcmd($cmd),$descriptorspec, $pipes);       
                $result=proc_get_status($proc);       
                while($result['running']==1){
                    sleep(3);
                    $result=proc_get_status($proc);                    
                } 
        return true;
}//enf of runcmd

function cut360p($_360p,$temp_360p,$strpos,$endpos){
    
    if ($strpos!=null and $endpos==null)
        if (runcmd("/bin/sh /var/www/videoedit/enc_ss_169.sh $strpos $_360p $temp_360p")) return true;;
    if ($strpos==null and $endpos!=null)
        if(runcmd("/bin/sh /var/www/videoedit/enc_t_169.sh $endpos $_360p $temp_360p")) return true;
    if ($strpos!=null and $endpos!=null)
        if(runcmd("/bin/sh /var/www/videoedit/enc_ss_t_169.sh $strpos $endpos $_360p $temp_360p")) return true;
}

function cut720p($_720p,$temp_720p,$strpos,$endpos){
    
    if ($strpos!=null and $endpos==null)
        if (runcmd("/bin/sh /var/www/videoedit/enc_ss_169.sh $strpos $_720p $temp_720p")) return true;
    if ($strpos==null and $endpos!=null)
        if(runcmd("/bin/sh /var/www/videoedit/enc_t_169.sh $endpos $_720p $temp_720p")) return true;
    if ($strpos!=null and $endpos!=null)
        if(runcmd("/bin/sh /var/www/videoedit/enc_ss_t_169.sh $strpos $endpos $_720p $temp_720p")) return true;
}

function runcut($dbh){
    
    $stmt=$dbh->prepare("SELECT id,tid,file_360p,file_720p,ss,t FROM videocut_tasks WHERE state=? AND error!=?");
    $stmt->execute(array('params_set',1)) or die(print_r($dbh->errorInfo()));
    $task=$stmt->fetchAll(\PDO::FETCH_ASSOC);
    if(!empty($task)){
        
        $tid=$task[0]['tid'];    
        $_360p=$task[0]['file_360p'];
        $_720p=$task[0]['file_720p'];
        $temp_360p=str_replace("_001", "_temporary", $_360p);
        $temp_720p=str_replace("_001", "_temporary", $_720p);    
        $strpos=$task[0]['ss'];
        $endpos=$task[0]['t'];
    
    
        if (cut360p($_360p,$temp_360p,$strpos,$endpos) and cut720p($_720p,$temp_720p,$strpos,$endpos)){
            $stmt=$dbh->prepare("UPDATE videocut_tasks SET file_360p=?,file_720p=?,state=? WHERE tid=?");
            $stmt->execute(array($temp_360p,$temp_720p,'cut_done',$tid)) or die(print_r($dbh->errorInfo()));        
        } else {        
            $stmt=$dbh->prepare("UPDATE videocut_tasks SET state=?,error=? WHERE tid=?");
            $stmt->execute(array('error',1,$tid)) or die(print_r($dbh->errorInfo()));        
            //echo "error - file not cutted";      //to-do log                              
        }
    }
}

function copydel($dbh){
    //select files chosen for copy
    $stmt=$dbh->prepare("SELECT tid,file_360p,file_720p FROM videocut_tasks WHERE state=?");
    $stmt->execute(array('copy_del')) or die(print_r($dbh->errorInfo()));
    $task=$stmt->fetchAll(\PDO::FETCH_ASSOC);
    if(!empty($task)){
        $_360pcopied=false;
        $_720pcopied=false;
        $tid=$task[0]['tid'];    
        $temp_360p=$task[0]['file_360p'];
        $temp_720p=$task[0]['file_720p'];
    
        //check if files exists on server   

        if (is_file("/mnt/wowzacontent/".$temp_360p)){        
            $_360p=str_replace("_temporary", "_001",$temp_360p);    
            if (copy("/mnt/wowzacontent/".$temp_360p, "/mnt/wowzacontent/".$_360p)){ 
                $stmt=$dbh->prepare("UPDATE videocut_tasks SET file_360p=?,ss=?,t=?,state=? WHERE tid=?");
                $stmt->execute(array($_360p,'','','task_init',$tid)) or die(print_r($dbh->errorInfo()));                    
                unlink("/mnt/wowzacontent/".$temp_360p) or die("file".$temp_360p."not deleted");//to-do log
            }                
            else {
                $stmt=$dbh->prepare("UPDATE videocut_tasks SET state=?,error=? WHERE tid=?");
                $stmt->execute(array('error',1,$tid)) or die(print_r($dbh->errorInfo()));
            }    
        }

        if (is_file("/mnt/wowzacontent/".$temp_720p)){        
            $_720p=str_replace("_temporary", "_001",$temp_720p);
            if (copy("/mnt/wowzacontent/".$temp_720p, "/mnt/wowzacontent/".$_720p)){        
                $stmt=$dbh->prepare("UPDATE videocut_tasks SET file_720p=?,ss=?,t=?,state=? WHERE tid=?");
                $stmt->execute(array($_720p,'','','task_init',$tid)) or die(print_r($dbh->errorInfo()));                                
                unlink("/mnt/wowzacontent/".$temp_720p) or die("file".$temp_720p."not deleted");
            }
            else {
                $stmt=$dbh->prepare("UPDATE videocut_tasks SET state=?,error=? WHERE tid=?");
                $stmt->execute(array('error',1,$tid)) or die(print_r($dbh->errorInfo()));
            }    
        }
    }    
}

function isFFmpegruning(){
    $fp = popen("ps ax | grep -E 'ffmpeg\s*-i.*' 2>&1", "r");
    $out=fread($fp, 1024);
    pclose($fp);
    if (empty($out))
        return false;
    return true;    
}

//$conn = mysqli_connect(DB_HOST, DB_LOGIN, DB_PASSWORD,DB_NAME);	
try {
    $dbh = new PDO(DSN, DB_LOGIN, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'BD connection error: ' . $e->getMessage();
}
$dbh->query("SET NAMES 'utf8'");

while (true){      
    
    if (isFFmpegruning())
        return;
    
    runcut($dbh);
    
    copydel($dbh);
    
    sleep(5);
}
?>
