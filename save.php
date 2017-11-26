<?php

$file = $_GET['file'];
$file = strip_tags($file);
$file = preg_replace('/[\\\\.]/i', '', $file);
if (!preg_match('/mp3$/', $file)) {
    echo "File extension is not allowed.";
    exit(1);
}

$file = preg_replace('/mp3$/', '.mp3', $file);
$filePath = dirname(__FILE__) .'/'. $file;
if (!is_file($filePath)) {
    echo "File not found.";
    exit(1);
}
$pos = strrpos($filePath, '/');
$filename = substr($filePath, $pos + 1, strlen($filePath) - $pos -1);
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . $filename . "\"");
@readfile($filePath);
