<?php


$allowed_counter_ip = array(
    '127.0.0.1'
);
$remote_addr        = $_SERVER['REMOTE_ADDR'];
$confirmation       = 0;

foreach ($allowed_counter_ip as $ip) {
    // change wildcard
    $ip = preg_replace('@\*$@i', '.', $ip);
    if ($ip == $remote_addr || $_SERVER['HTTP_HOST'] == 'localhost' || preg_match("@$ip@i", $ip)) {
        $confirmation = 1;
    }
}

if (!$confirmation) {
    header("location: index.php");
}

if (stripos(PHP_OS, 'Linux') !== false) {
 $gammu_dir = 'gammu/bin/linux/';
} else {
  $gammu_dir = 'gammu/bin/win/';
}

DEFINE('PATH',LIB.$gammu_dir);
require LIB.$gammu_dir.'gammu_class.php';