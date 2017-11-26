<?php
$chooseDir = !empty($_GET['dir']) ? $_GET['dir'] : "";
if (preg_match('/[\.\/\\]/', $chooseDir)) {
    echo "Illegal parameter.";
    exit(1);
}

//Get all directory.
$dirs = array(".");
$d = opendir('./');
while ($f = readdir($d)) {
    if ($f == '.' || $f == '..' ) continue;
    if (is_dir($f)) {
        $dirs[] = $f;
    }
}

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta charset='utf-8'>
</head>
<body>

<div>
    <form action="download.php" target="download" method="post">
    下載網址：<input name="url" type="text" value="" id="downloadUrl" style="width:300px;" placeholder="http://xxx.xx.xx/x.mp3"/><br />
    儲存目錄：
        <select name="dir">
<?php
        foreach ($dirs as $dir) {
            if ($dir == '.' || $dir == '..') continue;
            echo "<option value=\"$dir\">$dir</option>";
        }
?> 
        </select> <span style="color: #ccc; font-size: 13px;">注意資料夾權限</span><br />
    儲存檔名：<input name="name" type="text" value=""  style="width:300px;" placeholder="perfect_honey"/>
    <br /><button>下載</button>
    </form>
    <iframe name="download" style="width:0;height:0px; display:none;"></iframe>
</div>

<br />
<div style="margin 5px 0;">
Choose directory : <select onchange="chooseDirectory(event); return false;">
<?php
        foreach ($dirs as $dir) {
            $name = $dir;
            if ($dir == '.' || $dir == '..') {
                $dir = "";
                $name = "/";
            }
            $selected = ($dir == $chooseDir) ? "selected = 'true'" : "";
            echo "<option value=\"$dir\" $selected>$name</option>";
        }
?> 
</select>
</div>
<div id="playing-wrap">
    <span>Playing: </span> <span id="playing-song-name"></span>
</div>
<div style="margin:20px;">

<?php


$files = array();
getFiles('./'. $chooseDir, $files);

shuffle($files);

foreach ($files as $file) {
    $source= '<source src="' . $file .'" />';
    break;
}
echo <<<HTML
    <div id="audio">
        <audio controls  volume=0.4 id="player" autoplay=true>
            $source
        </audio>
    </div>
HTML;

echo '<br />';

foreach ($files as $file) {
    echo '<div style="height: 25px; margin: 1px 0;" ><a href="'. urlencode($file).'"  onclick="playThis(\'' .$file.'\');return false;">' . preg_replace('/^\.\/\//', '', $file) .'</a>  / <a style="color:#a00;" href="save.php?file='.preg_replace('/^\.\/\//', '',$file) .'">Save File</a><br />'. "</div>\n";
}

foreach ($files as &$file) {
    $file = preg_replace('/^\.\/\//', '', $file);
    $file = preg_replace("/\'/", "\\'", $file);

}


function getFiles($dir, &$files) {
    $dh = opendir($dir);
    if ($dh) {
        while (($file = readdir($dh) )!== false) {
            if ($file == '.' || $file == '..') continue;
            if (is_dir($dir. $file)) {
                getFiles($dir. '/'. $file, $files);
            } else if (preg_match('/(mp3)/i', $file)) {
                $files[] = $dir . '/'. $file;
            }
        }
    }
    
}

?>




</div>



</body>
<script>
var musicList = ['<?php echo implode("','", $files)?>'];
var index = Math.round(Math.random() * (musicList.length - 1 ));

function init() {
    var player = document.getElementById('player');
    player.volume = 0.2;
    player.addEventListener('ended', playNext);
}


function playNext() {
    var file;
    index++;
    if (index >= musicList.length) index = 0;
    else if (index < 0) index = 0;
    file = musicList[index];
    playThis(file);
}

function playThis(file) {
    //console.log("play file: " + file);
    var player = document.getElementById('player');
    var playerSource;
    if (player) {
        playerSource = player.querySelector("source");
    }
    playerSource.src = file;
    location.hash = file;
    var songName = file.split(/\//);
    songName = songName[songName.length - 1];
    songName = songName.replace(/\.[^\.]+$/, '');
    document.getElementById('playing-song-name').innerHTML = songName;
    if (player) {
        player.load();
        player.play();
    }
}

function findMusicByName(name) {
    var index = 0, n;
    n = musicList.length;
    for (index; index < n; index++) {
        if (musicList[index] == name) {
            return index;
        }
    }
    return 1;
}

function chooseDirectory(E) {
    var target;
    target = E.currentTarget;
    window.location = "?dir=" + target.value;
    return true;
}

init();
var hash = location.hash;
if (hash) {
    hash = hash.replace(/^[#\/\/\.]+/, '');
    index = findMusicByName(hash) - 1;
    playNext();
} else {
    playNext();
}

</script>

</html>
