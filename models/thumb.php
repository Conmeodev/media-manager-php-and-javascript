<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
include_once $_SERVER['DOCUMENT_ROOT'] . "/_set.php";

header('Content-Type: text/plain; charset=utf-8');

$act = isset($_POST['act']) ? $_POST['act'] : 'get';
$id  = isset($_POST['id']) ? _sql($_POST['id']) : null;

if (!$id) {
    exit;
}

$data = _fetch("SELECT * FROM files WHERE id = '$id'");
if (!$data) {
    exit;
}

$thumb_folders = "_thumbs/" . $data['_uid'] . "/" . $data['_byid'];
$thumb_name    = $data['_token'];
$thumbPath     = "/$thumb_folders/$thumb_name.png";

// ========== ACT: GET ==========
if ($act === "get") {
    if (!empty($data['_thumb'])) {
        echo $data['_thumb'];
        exit;
    }

    // Ảnh tĩnh
    if (_tf($data['_type']) === "image") {
        if (!is_dir($root . "/" . $thumb_folders)) {
            mkdir($root . "/" . $thumb_folders, 0777, true);
        }
        _imageThumb("10240", $root . $data['_dir'], $root . "/" . $thumb_folders . "/" . $thumb_name . ".png");
        _query("UPDATE files SET _thumb = '$thumbPath' WHERE id = '$id'");
        echo $thumbPath;
        exit;
    }

    // Video - VPS
    if (_tf($data['_type']) === "video" && $upload_thumb_vps === "on") {
        if (!is_dir($root . "/" . $thumb_folders)) {
            mkdir($root . "/" . $thumb_folders, 0755, true);
        }
        $videoLink = $http . $data['_dir'];
        $link_vps_thumb = @file_get_contents("http://$ip_vps/thumbVideo.php?act=CREATE_THUMB&linkVideo=http://$domain$videoLink");

        if ($link_vps_thumb) {
            _imageThumb("102400", $link_vps_thumb, "$root/$thumb_folders/$thumb_name.png");
            _query("UPDATE files SET _thumb = '$thumbPath' WHERE id = '$id'");
            echo $thumbPath;
            exit;
        }
    }

    exit; // Không có thumbnail
}

// ========== ACT: THUMB ==========
if ($act === "thumb") {
    $token   = isset($_POST['token']) ? _sql($_POST['token']) : null;
    $imgData = isset($_POST['imgData']) ? $_POST['imgData'] : null;

    if (!$token || !$imgData || strpos($imgData, 'data:image') !== 0) {
        exit;
    }

    if ($data['_token'] !== $token) {
        exit;
    }

    if (!is_dir($root . "/" . $thumb_folders)) {
        mkdir($root . "/" . $thumb_folders, 0755, true);
    }

    if (_videoThumb($thumb_folders, $thumb_name, $imgData)) {
        _query("UPDATE files SET _thumb = '$thumbPath' WHERE id = '$id'");
        //echo $thumbPath;
        exit;
    }

    exit;
}

exit;
