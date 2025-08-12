<?php
ob_start();
session_start();
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
$root = $_SERVER["DOCUMENT_ROOT"];
include_once __DIR__.'/_root.php';
include_once __DIR__.'/include/url.php';
include_once __DIR__.'/_config.php';
include_once __DIR__.'/_functions.php';
include_once __DIR__.'/_system.php';
include_once __DIR__.'/lib/api.php';
$user = getUserByToken();
$admin = admin();
include_once __DIR__.'/_meta.php';
