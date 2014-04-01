<?php
namespace VideoAdmin\Model;
if ($_POST['check1']!=null){
        $src_360p=str_replace("_temporary", "_001",$this->_360p);        
        (isset($this->_720p)) ? $scr_720p=str_replace("_temporary", "_001",$this->_720p) : $scr_720p=null;        
        Videocut::remakeCut($src_360p,$scr_720p);        
        header("HTTP/1.1 302 Found");
        header("Location: ".$this->tid);          
        exit;            
    }  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru" xml:lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <link rel="stylesheet" href="/css/admin.css" />
    <style>
        body {color: #5A7087; font-size: 1em; font-family: Arial;}
    </style>
</head>
    <body style="margin:0px 0px 0px 0px;"> 
        <form action="<?=$this->tid?>" method="POST" id="copy">           
            <p><b>Произошла ошибка, попробовать еще раз?</b></p>
            <input type="checkbox" size="1" name="check1">            
            <input type="submit" value="подтвердить">                   
        </form>
    </body>
</html>
