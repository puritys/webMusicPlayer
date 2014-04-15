<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
</head>
<body>
<?php


$files = array();
getFiles('./', $files);




shuffle($files);

echo '<div id="audio"><audio controls  volume=0.4 id="player" >';
foreach ($files as $file) {
    echo '<source src="' .$file. '" type="audio/ogg">';
}
echo '</audio></div>';

echo '<br />';

foreach ($files as $file) {
    echo '<a href="'. urlencode($file).'"  onclick="playThis(\'' .$file.'\');return false;">' . $file .'</a> <br />';
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








</body>
<script>
var musicList = [<?php echo explode(",", $files)?>];
var index = 0;
function init() {
    var player = document.getElementById('player');
    player.volume = 0.2;
    player.onended = playNext;
}

function end() {
    
}

function playNext() {
    var file;
    index++;
    if (index >= musicList.length) index = 0;
    file = musicList[index];
    playThis(file);
}

function playThis(file) {
    var html = "";
    html += '<audio controls  volume=0.4 id="player" autoplay=true>';
    html +=  '<source src="' + file + '" type="audio/ogg">';
    html += '</audio>';
    document.getElementById('audio').innerHTML = html;
    init();
}



init();
</script>
</html>
