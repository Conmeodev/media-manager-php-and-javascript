<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
include_once $_SERVER['DOCUMENT_ROOT'].'/_set.php';
header("Content-Type: application/javascript");
include_once __DIR__.'/functions.php';
include_once __DIR__.'/main.php';
include_once __DIR__.'/xulyanh.php';
if($admin) {
	include_once __DIR__.'/adminJs/upload.php';
	include_once __DIR__.'/adminJs/main.php';
}
