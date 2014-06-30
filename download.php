<?php

$url = $_POST['url'];
$name = $_POST['name'];
require_once "webClient.php";
$name = strip_tags($name);
$name = preg_replace('/[^0-9a-z]/i', '', $name);

$c = new MyCurl();
$saveToPath = dirname(__FILE__) . '/other/'. $name.'.mp3';
$c->download($url, $saveToPath);


