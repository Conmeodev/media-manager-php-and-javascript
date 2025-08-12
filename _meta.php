<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
$getFolders = $_GET['getFolders'] ?? null;
$getFiles = $_GET['getFiles'] ?? null;
//echo $getFiles;
$notFolders = array(
	'id'         => 0,
	'_uid'       => 0,
	'_byid'      => 0,
	'_name'      => "Thư mục này không tồn tại",
	'_content'   => '',
	'_thumb'     => '/asset/image/folders-thumb-default.png',
	'_type'      => 'folder',
	'_tinhtrang' => 'active',
	'keysearch'  => '',
	'_token'     => '',
	'_time'      => time()
);
$notFiles = array(
	'id'         => 0,
	'_uid'       => 0,
	'_byid'      => 0,
	'_name'      => "Tập tin này không tồn tại",
	'_content'   => '',
	'_thumb'     => '/asset/image/image-files-thumb-default.png',
	'_type'      => 'files',
	'_tinhtrang' => 'active',
	'keysearch'  => '',
	'_token'     => '',
	'_time'      => time()
);
$history_type = "link";
if(empty($getFolders) && empty($getFiles)) {
	$getFolders = 0;
}
else if(!empty($getFolders)) {
	if($admin) {$inFolders = _fetch("SELECT * FROM folders WHERE id='$getFolders'");}
	else {$inFolders = _fetch("SELECT * FROM folders WHERE id='$getFolders' and _tinhtrang='danghoatdong'");}
	if(!isset($inFolders['id'])) {
		$inFolders = $notFolders;
	}
	$history_type = "folders";
}
else if(!empty($getFiles)) {
	if($admin) {
		$inFiles = _fetch("SELECT * FROM files WHERE id='$getFiles'");

	}
	else {
		$inFiles = _fetch("SELECT * FROM files WHERE id='$getFiles' and _tinhtrang='danghoatdong'");
		$fileInFolders = _fetch("SELECT * FROM folders WHERE id='".$inFiles['_byid']."' and _tinhtrang='danghoatdong'");
		if(!isset($fileInFolders['id']) && $inFiles['_byid'] != 0) {    	
			$inFiles = $notFiles;
		}
	} 
	
	
	if(!isset($inFiles['id'])) {
		$inFiles = $notFiles;

	}
	$history_type = "files";
}

$title = $inFiles['_name'] ?? ($inFolders['_name'] ?? "File Manager Gallery");
$meta_thumb = $inFiles['_thumb'] ?? ($inFolders['_thumb'] ?? "/asset/image/default-meta-img.png");
$meta_description = $inFiles['_name'] ?? ($inFolders['_name'] ?? $domain);
$meta_keywords = $inFiles['_name'] ?? ($inFolders['_name'] ?? $domain);
$history  = $inFiles['id'] ?? ($inFolders['id'] ?? null);
