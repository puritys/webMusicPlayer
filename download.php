<?php

$url = $_POST['url'];

require_once "webClient.php";

$c = new MyCurl();
$saveToPath = dirname(__FILE__) . '/other/test.mp3';
$c->download($url, $saveToPath);


