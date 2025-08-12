<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
include_once $_SERVER['DOCUMENT_ROOT']."/_set.php";
$act = isset($_POST['act']) ? $_POST['act'] : "";
$res = "false";
$title = "Thất Bại";
$content = "<ul>";
$ok = 0;

if(if5($act,"rename") && $user) {
    $name = isset($_POST["name"]) ? $_POST["name"] : null;
    $id = isset($_POST["id"]) ? $_POST["id"] : null;
    $files = _files($id);
    if($files["_uid"] == $user['id']) {
        $name1 = _sql($name);
        $update = _query("UPDATE files SET _name='$name1' WHERE id='$id' ");
        if($update) {
            $res = "true";
            $title = "Thành Công";
            $content .= "Thay đổi tên tập tin thành công.";
        } else {
            $content .= "Đã xảy ra lỗi khi thay đổi tên tập tin.";
        }
    } else {
        $content .= "Bạn không có quyền sửa đổi tập tin này.";
    }
}
else if(if5($act,"setThumbFolders") && $admin) {
    $rootfolders = isset($_POST['root']) ? $_POST['root'] : 0;
    $idf = isset($_POST['id']) ? $_POST['id'] : 0;
    $file = _fetch("SELECT * FROM files WHERE id='$idf'");
    if($rootfolders != 0) {
        $id = $file["_byid"];
    } else {
        $id = getRootFolder($idf)[0];
    }
    $thumb = $file["_thumb"];
    $is_folders = _fetch("SELECT * FROM folders WHERE id='$id'");
    if(isset($is_folders['id'])) {
        $update = _query("UPDATE folders SET _thumb='$thumb' WHERE id='$id'");
        if($update) {
            $title = "Cập nhật thumbnail thành công. $id-$rootfolders";
        } else {
            $title = "Có lỗi xảy ra.";
        }
    } else {
        $title = "Không tồn tại folders id $id .";
    }
    
}
else if(if5($act,"delThumb") && $admin) {
    $idf = isset($_POST['id']) ? $_POST['id'] : 0;
    $sel = _fetch("select * from files where id='$idf'");
    $update = _query("UPDATE files SET _thumb='' WHERE id='$idf'");
    $del = unlink($_SERVER['DOCUMENT_ROOT'].$sel['thumb']);
        if($update) {
            $title = "Xoá Thumbnail thành công";
        } else {
            $title = "Có lỗi xảy ra.";
        }
}
else {
    $content .= "Không tìm thấy thao tác.";
}
$content .= "</ul>".$user["id"];
$return = ["res" => $res, "title" => $title, "content" => $content];
echo json_encode($return);