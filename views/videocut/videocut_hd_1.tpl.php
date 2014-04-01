<?php
namespace VideoAdmin\Model;

 if ($_SERVER["REQUEST_METHOD"]=="POST"){    
    unset($ss);
    $strpos=array(
        "h"=>(int)trim($_POST['ss_h']),
        "m"=>(int)trim($_POST['ss_m']),
        "s"=>(int)trim($_POST['ss_s'])
    );      
    unset($t);
    $endpos=array(
        "h"=>(int)trim($_POST['t_h']),
        "m"=>(int)trim($_POST['t_m']),
        "s"=>(int)trim($_POST['t_s'])        
    );    
    Videocut::setCutParams($strpos,$endpos);    
    header("HTTP/1.1 302 Found");
    header("Location: ".$this->tid);
    exit();               
 }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru" xml:lang="ru">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title></title>
    <link rel="stylesheet" href="/css/admin.css" />
    <link rel="stylesheet" type="text/css" href="/css/player-styles/style.css" />
    <style>
        body {color: #5A7087; font-size: 1em; font-family: Arial;}
    </style>

</head>
    <body style="margin:0px 0px 0px 0px;">    
        <script type="text/javascript" src="<?=Videocut::PLAYER_JS ?>"></script>
        <script type="text/javascript">
            function cp(obj) {
                for (var i = 1; i < arguments.length; i++) {
                    var arg = arguments[i];
                    for (var p in arg) if (arg.hasOwnProperty(p)) {obj[p] = arg[p];}
                }
                return obj;
            }

            var visSettings = {
                "skin": "<?=Videocut::PLAYER_SKIN;?>",
                "flashplayer":"<?=Videocut::PLAYER_?>",
                "image":"",
                "controlbar": "bottom",
                "logo.out": 1,
                "plugins": { 
                    "<?=Videocut::PLAYER_HD?>": {
                        "files_360p":"<?=$this->_360p?>",
                        "files_720p":"<?=$this->_720p?>"
                    },
                    "<?=Videocut::PLAYER_RS?>": {"link":"http://russiasport.ru"},
                    "<?=Videocut::PLAYER_TIMESLIDER?>": {"fontcolor":"0xFFFFFF"}		
                }
            };
        </script>    
        <div id="noheader">Ваш браузер не поддерживает воспроизведение видео.</div>
        <script type="text/javascript">
            var setup;
            settings=<?=Videocut::SETTINGS?>  
            if (navigator.userAgent.match(/Android 3|Android 4/i)) {
                setup = cp(settings, visSettings, {
                    "modes": [
                    { type: 'html5', config:{"file":"<?=Videocut::buildstreamer("rtsp","un4Aewo7ut",$this->_360p)?>","provider":"video"} }
                    ]
                });
            jwplayer("noheader").setup(setup);
            } else if (navigator.userAgent.match(/iPhone|iPad|iPod/i)) {
                setup = cp(settings, visSettings, {
                    modes: [
                            { type: 'html5', config:{"file":"<?=Videocut::buildstreamer("http","un4Aewo7ut",$this->_360p)?>","provider":"video"} }
                    ]
                });
                jwplayer("noheader").setup(setup);
            } else {
                setup = cp(settings, visSettings, {
                provider: '<?=Videocut::PROTO ?>',
                streamer: '<?=Videocut::buildstreamer("rtmp","un4Aewo7ut")?>',
                file: '<?=$this->_360p?>'
                });
                jwplayer("noheader").setup(setup);
            }
                //console.dir(setup);
                
            function hhmmss_errors(){            
                var error=false;
                var ss_h = document.forms["cut"].elements["ss_h"].value;
                var ss_m = document.forms["cut"].elements["ss_m"].value;
                var ss_s = document.forms["cut"].elements["ss_s"].value;
                var t_h = document.forms["cut"].elements["t_h"].value;
                var t_m = document.forms["cut"].elements["t_m"].value;
                var t_s = document.forms["cut"].elements["t_s"].value;
                if (ss_h > 23 || t_h > 23){
                    error=true;
                }
                if (ss_m > 59 || t_m > 59){
                    error=true;
                }
                if (ss_s > 59 || t_s > 59){
                    error=true;
                }
                if (!error){                    
                    document.getElementById("cut").submit();
                } else{
                    alert("Ошибка, введите время в правильном формате h<24:m<60:s<60");
                }
            }             
        </script>   
    <ul class="translation-channels" style="width:560px;">        
<?php   
    
    $tr = new Translation();
    $currentTid = $_GET['id'];
    $channels = $tr->getChannels($this->tid);
    if (empty($channels))
        $channels = $tr->getChannels($tr->getParentId($this->tid));    
    if (!empty($channels)){
        foreach ($channels as $channel) {  
            if ($channel['translation_id'] == $currentTid && $channel['child_translation_id'] == 0) {            
?>        
                <li class="active"><?php echo htmlspecialchars($channel['name']); ?></li>
<?
                } else if ($channel['child_translation_id'] == $currentTid && $channel['child_translation_id'] !=0) {                

?>                
                    <li class="active"><?php echo htmlspecialchars($channel['name']); ?></li>
<?
                } else {
                    $url = $channel['child_translation_id'];            
                    if ($channel['translation_id']!=$currentTid && $channel['child_translation_id'] == 0)
                        $url = $channel['translation_id'];            
?>
                    <li><a href="<?php echo htmlspecialchars($url); ?>" target="_self"><?php echo htmlspecialchars($channel['name']); ?></a></li>
<?
                }
        }
?>
    </ul>
<?
    }
?>

        
        <form action="<?=$this->tid?>" method="POST" id="cut">            
            <p><b>Начальное время кодирования</b></p>
            <table border="0">
                <tr>
                    <td style='margin:0 auto;text-align:center'>hours</td><td style='margin:0 auto;text-align:center'>mins</td><td style='margin:0 auto;text-align:center'>secs</td>
                </tr>
                <tr>
                    <td style='margin:0 auto;text-align:center'><input type="input"  size="1" name="ss_h">:</td><td style='margin:0 auto;text-align:center'><input type="input" size="1" name="ss_m">:</td><td style='margin:0 auto;text-align:center'><input type="input"  size="1" name="ss_s"></td>
                </tr>                                
            </table>
            <p><b>Конечное время кодирования</b></p>
            <table border="0">
                <tr>
                    <td style='margin:0 auto;text-align:center'>hours</td><td style='margin:0 auto;text-align:center'>mins</td><td style='margin:0 auto;text-align:center'>secs</td>
                </tr>
                <tr>
                    <td style='margin:0 auto;text-align:center'><input type="input"  size="1" name="t_h">:</td><td style='margin:0 auto;text-align:center'><input type="input" size="1" name="t_m"></td><td style='margin:0 auto;text-align:center'>:<input type="input"  size="1" name="t_s"></td>
                </tr>                                
            </table><br>            
            <input type="button" size="10" value="Обрезать" onClick="hhmmss_errors();">
        </form>
    </body>
</html>