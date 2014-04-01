<?php
namespace VideoAdmin\Model;
if ($_SERVER["REQUEST_METHOD"]=="POST"){
    if ($_POST['check1']!=null and $_POST['check2']==null){            
        Videocut::CopyandDeleteFiles();
        header("HTTP/1.1 302 Found");
        header("Location: ".$this->tid);  
        exit;            
    }        
    if ($_POST['check2']!=null and $_POST['check1']==null){        
        $_360p=str_replace("_temporary", "_001",$this->_360p);        
        $_720p=str_replace("_temporary", "_001",$this->_720p);
        Videocut::remakeCut($_360p,$_720p);
        header("HTTP/1.1 302 Found");
        header("Location: ".$this->tid);  
        exit;            
    }
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
        <script type="text/javascript" src="<?= Videocut::PLAYER_JS ?>"></script>    
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
                    provider: '<?= Videocut::PROTO ?>',
                    streamer: '<?=Videocut::buildstreamer("rtmp","un4Aewo7ut")?>',
                    file: '<?=$this->_360p?>'
                    });
                    jwplayer("noheader").setup(setup);
                }
                //console.dir(setup);
            </script>    
            <form action="<?=$this->tid?>" method="POST" id="copy">   
                <table border="0">
                    <tr>
                        <td style='margin:0 auto;text-align:center'><p><b>Скопировать файлы на сервер</b></p></td><td style='margin:0 auto;text-align:center'><p><b> или обрезать заново?</b></p></td>
                    </tr>        
                    <tr>
                        <td style='margin:0 auto;text-align:center'><input type="checkbox" size="1" name="check1"></td><td style='margin:0 auto;text-align:center'><input type="checkbox" size="1" name="check2"></td>                
                    </tr>
                    <tr>
                        <td colspan="2" style='margin:0 auto;text-align:center'><input type="submit" value="подтвердить"></td>
                    </tr>
                </table>
            </form>
     </body>
</html>
