<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
$config_file_exists = file_exists('_db_config.php');
if(!$config_file_exists) {
	header("location: /install.php");
	exit();
}
$index_file = $_GET['index_file'] ?? "main.php";
include_once "views/template/head.php";
include_once "views/template/$index_file";
include_once "views/template/end.php";

