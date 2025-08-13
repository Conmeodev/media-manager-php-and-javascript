<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
*/
include_once $_SERVER['DOCUMENT_ROOT'] . "/_set.php";

header('Content-Type: application/json; charset=utf-8');

$res = "false";
$title = "Thất bại";
$content = "<ul>";
$ok = 1; // 0 = success (theo style một số file), nhưng ở đây mình giữ ok=1 là có lỗi, ok=0 là thành công

// Phải đăng nhập
if (!$user) {
    $content .= "<li>Bạn cần <a href='/views/login.php#login'>đăng nhập</a> để đổi mật khẩu.</li>";
    $content .= "</ul>";
    echo json_encode(["res"=>$res, "title"=>$title, "content"=>$content]);
    exit;
}

// CSRF (tùy chọn, nếu bạn muốn dùng)
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if (isset($_POST['csrf_token']) && $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $content .= "<li>CSRF token không hợp lệ.</li>";
    $content .= "</ul>";
    echo json_encode(["res"=>$res, "title"=>$title, "content"=>$content]);
    exit;
}

// Nhận dữ liệu
$old = isset($_POST['old_password']) ? trim($_POST['old_password']) : "";
$new = isset($_POST['new_password']) ? trim($_POST['new_password']) : "";
$re  = isset($_POST['re_password'])  ? trim($_POST['re_password'])  : "";
$logout_all = isset($_POST['logout_all']) ? $_POST['logout_all'] : "0";

// Validate cơ bản
if ($old === "" || $new === "" || $re === "") {
    $content .= "<li>Vui lòng nhập đầy đủ thông tin.</li>";
    $content .= "</ul>";
    echo json_encode(["res"=>$res, "title"=>$title, "content"=>$content]);
    exit;
}
if ($new !== $re) {
    $content .= "<li>Mật khẩu mới nhập lại không khớp.</li>";
    $content .= "</ul>";
    echo json_encode(["res"=>$res, "title"=>$title, "content"=>$content]);
    exit;
}
if (strlen($new) < 3) {
    $content .= "<li>Mật khẩu mới tối thiểu 3 ký tự.</li>";
    $content .= "</ul>";
    echo json_encode(["res"=>$res, "title"=>$title, "content"=>$content]);
    exit;
}

// Kiểm tra mật khẩu cũ theo cách cũ (if5 -> so md5 hai chuỗi)
// Lưu ý: hệ thống hiện tại lưu _matkhau dạng plain (không hash)
if (!if5($old, $user['_matkhau'])) {
    $content .= "<li>Mật khẩu cũ không chính xác.</li>";
    $content .= "</ul>";
    echo json_encode(["res"=>$res, "title"=>$title, "content"=>$content]);
    exit;
}

// Cập nhật mật khẩu mới
$uid = _sql($user['id']);
$new_safe = _sql($new);
$update = _query("UPDATE `user` SET `_matkhau` = '$new_safe' WHERE `id` = '$uid'");

if ($update) {
    // Tuỳ chọn: đăng xuất tất cả thiết bị đang đăng nhập
    if ($logout_all === "1") {
        _query("UPDATE `login` SET `_tinhtrang`='dangxuat' WHERE `_uid`='$uid'");
        // Xoá token hiện tại trên trình duyệt
        setcookie("_token", "", time() - 3600, "/", "", true, true);
    }

    $res = "true";
    $title = "Thành công";
    $ok = 0;
    $content = "<ul><li>Đổi mật khẩu thành công.</li>";
    if ($logout_all === "1") {
        $content .= "<li>Đã đăng xuất tất cả thiết bị. Vui lòng đăng nhập lại.</li>";
    }
    $content .= "</ul>";
} else {
    $content .= "<li>Có lỗi khi cập nhật mật khẩu.</li></ul>";
}

echo json_encode(["res"=>$res, "title"=>$title, "content"=>$content]);
