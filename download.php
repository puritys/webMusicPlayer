<?php

$url = $_POST['url'];
$name = $_POST['name'];
$dir = $_POST['dir'];
if (empty ($dir)) $dir = "./";
require_once "webClient.php";
$name = strip_tags($name);
$name = preg_replace('/[\/\\\\.]/i', '', $name);
$dir  = preg_replace('/[\.]{2,}/i', '', $dir);


$c = new MyCurl();
$saveToPath = dirname(__FILE__) . '/' . $dir. '/'. $name.'.mp3';
$c->download($url, $saveToPath);


