<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
    <meta charset='utf-8'>
</head>
<body>

<div>
    <form action="download.php" target="download" method="post">
    網址：<input name="url" type="text" value="" id="downloadUrl" style="width:300px;" /><br />
    檔名：<input name="name" type="text" value=""  style="width:300px;" />
    <br /><button>下載</button>
    </form>
    <iframe name="download" style="width:0;height:0px; display:none;"></iframe>
</div>

<div style="margin:20px;">

<?php


$files = array();
getFiles('./', $files);




shuffle($files);

echo '<div id="audio"><audio controls  volume=0.4 id="player" >';
foreach ($files as $file) {
 //   echo '<source src="' .$file. '" type="audio/ogg">';
}
echo '</audio></div>';

echo '<br />';

foreach ($files as $file) {
    echo '<a href="'. urlencode($file).'"  onclick="playThis(\'' .$file.'\');return false;">' . $file .'</a> <br />';
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

function reinit() {
    var player = document.getElementById('player');
    player.volume = 0.2;
    player.addEventListener('ended', playNext);
}


function playNext() {
    //console.log("next");
    var file;
    index++;
    if (index >= musicList.length) index = 0;
    else if (index < 0) index = 0;
    file = musicList[index];
    playThis(file);
}

function playThis(file) {
    var html = "";
    html += '<audio controls  volume=0.4 id="player" autoplay=true>';
    html +=  '<source src="' + file + '" type="audio/ogg">';
    html += '</audio>';
    document.getElementById('audio').innerHTML = html;
    location.hash = file;
    reinit();
}

function findMusicByName(name) {
    var index = 0, n;
    n = musicList.length;
    for (index; index < n; index++) {
        //console.log("-" + musicList[index] + "-");
        if (musicList[index] == name) {
            return index;
        }
    }
    return 1;
}

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
