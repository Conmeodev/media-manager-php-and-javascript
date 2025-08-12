<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
include_once __DIR__.'/../_set.php';
$act = $_POST['act'] ?? null;
$res = "false";
$title = "Hi";
$content = "<ul>";
if (if5($act, "mylove")) {
	$path = isset($_POST['path']) ? $_POST['path'] : "0";
	$type = isset($_POST['type']) ? $_POST['type'] : "";

	if (!$user) {
		$title = "Bạn phải đăng nhập để sử dụng chức năng này.";
	} else {
		$check = _query("SELECT * FROM mylove WHERE _uid = '".$user['id']."' AND _byid = '$path' AND _type = '$type'");

		if (mysqli_num_rows($check) > 0) {
			_query("DELETE FROM mylove WHERE _uid = '".$user['id']."' AND _byid = '$path' AND _type = '$type'");
			$title = "Đã bỏ khỏi mục yêu thích";
		} else {
			$insert = _query("INSERT INTO mylove (_uid, _byid, _type, _time) VALUES ('".$user['id']."', '$path', '$type', '$time')");
			if ($insert) {
				$res = "true";
				$title = "Đã lưu vào yêu thích";
			} else {
				$title = "Lỗi không xác định";
			}
		}
	}
}
if (if5($act, "move_trash")) {
    $path = isset($_POST['path']) ? $_POST['path'] : "0";
	$type = isset($_POST['type']) ? $_POST['type'] : "";
    if(!$admin) {$content = "Bạn không có quyền thực hiện thao tác này";} else {
        $move_trash = _query("UPDATE $type SET _tinhtrang='thungrac' WHERE id='$path'");
        if($move_trash) {
            $content = 'Đã chuyển vào thùng rác.';
        } else {
            $content = 'Thao tác thất bại.';
        }
    }
}
$content .= "</ul>";
$return = ["res" => $res, "title" => $title, "content" => $content];
echo json_encode($return);
?>