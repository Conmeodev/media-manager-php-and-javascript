<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
function auto_login($id) {
    GLOBAL $ketnoi;
    
    $user = _fetch("SELECT * FROM `user` WHERE `id` = '" . _sql($id) . "' LIMIT 1");
    if (!$user) {
        return false; // Không tìm thấy user
    }

    // Kiểm tra nếu user đã có token hợp lệ chưa
    $login = _fetch("SELECT * FROM `login` WHERE `_uid` = '$id' AND `_tinhtrang` = 'active' ORDER BY `_time` DESC LIMIT 1");

    if ($login) {
        // Nếu đã có token hợp lệ => đặt lại cookie (auto-login)
        setcookie("_token", $login['_token'], time() + 2592000, "/", "", true, true);
        $_COOKIE['_token'] = $login['_token']; 
        return true;
    }

    // Nếu chưa có token hợp lệ => tạo token mới
    $token = hash('sha256', $id . time() . $_SERVER['REMOTE_ADDR'] . bin2hex(random_bytes(8)));
    $device = $_SERVER['HTTP_USER_AGENT'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $time = time();

    // Lưu token mới vào database
    _query("INSERT INTO login (_uid, _matkhau, _ip, _time, _tinhtrang, _device, _token) 
            VALUES ('" . _sql($id) . "', '" . _sql($user['_matkhau']) . "', '" . _sql($ip) . "', '" . _sql($time) . "', 'dangnhap', '" . _sql($device) . "', '" . _sql($token) . "')");

    // Lưu token vào cookie
    setcookie("_token", $token, time() + 2592000, "/", "", true, true);
    $_COOKIE['_token'] = $token; 

    return true;
}

function login($taikhoan, $matkhau) {
    global $ketnoi, $ip, $time, $device;

    // Lấy thông tin user
    $user = _fetch("SELECT * FROM `user` WHERE `_taikhoan` = '" . _sql($taikhoan) . "' LIMIT 1");
    if (!$user) {
        return "Tài khoản không tồn tại!";
    }
    if (!if5($matkhau, $user['_matkhau'])) {
        return "Sai mật khẩu!";
    }

    // Tạo token mới (hash SHA256)
    $uid = $user['id'];
    $token = hash('sha256', $uid . $time . $ip . bin2hex(random_bytes(16)));

    // Thêm token mới (KHÔNG XOÁ token cũ)
    _insert("login", ["_uid", "_matkhau", "_ip", "_time", "_tinhtrang", "_device", "_token"], 
        [$uid, $user['_matkhau'], $ip, $time, "dangnhap", $device, $token]);

    // Lưu vào cookie (thời gian sống 30 ngày)
    setcookie("_token", $token, time() + 2592000, "/", "", true, true);

    return true;
}

function _user($col, $str) {
    $user = _fetch("SELECT * FROM user WHERE " . _sql($col) . "='" . _sql($str) . "'");
    return $user ? $user : null;
}

function generateToken($length = 64) {
    return bin2hex(random_bytes($length / 2));
}
function getUserByToken() {
    global $ketnoi;

    if (!isset($_COOKIE['_token'])) {
        return null;
    }

    $token = _sql($_COOKIE['_token']);
    $login = _fetch("SELECT * FROM `login` WHERE `_token` = '$token' AND `_tinhtrang` = 'dangnhap'");

    if (!$login) {
        return null;
    }

    // Kiểm tra nếu IP & User-Agent khớp với lúc đăng nhập
    /*if ($login['_ip'] !== $_SERVER['REMOTE_ADDR'] || $login['_device'] !== $_SERVER['HTTP_USER_AGENT']) {
        return null; // Token không hợp lệ
    }*/

    // Lấy thông tin user
    return _fetch("SELECT * FROM `user` WHERE `id` = '" . _sql($login['_uid']) . "'");
}

function logout() {
    GLOBAL $ketnoi,$user;

    $uid = $user['_id'];
    $token = $_COOKIE['_token'] ?? '';

    _query("UPDATE `login` SET `_tinhtrang` = 'dangxuat' WHERE `_uid` = '" . _sql($uid) . "' AND `_token` = '" . _sql($token) . "'");
    setcookie("_token", "", time() - 3600, "/", "", false, true);

    return "Đăng xuất thành công!";
}
function admin() {
    GLOBAL $user;
    if($user) {
        if($user['_capdo'] =="10") {
            return true;
        }
    }
    return false;
}