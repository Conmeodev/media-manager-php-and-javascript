<?php
include_once $_SERVER['DOCUMENT_ROOT'] . "/_set.php";
if (!$user) { header("Location: /views/login.php#login"); exit; }

// Tạo CSRF token nếu chưa có
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đổi mật khẩu</title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
.form-change-pass{max-width:420px;margin:50px auto;padding:20px;background:#fff;border-radius:10px;box-shadow:0 0 10px #ccc}
.form-change-pass h2{text-align:center;margin-bottom:16px}
.form-change-pass input{width:100%;padding:10px;margin-bottom:12px;border:1px solid #ddd;border-radius:6px}
.row{display:flex;align-items:center;gap:8px;margin:6px 0}
.btn{padding:10px 12px;border:0;border-radius:6px;cursor:pointer}
.btn-primary{background:#2196f3;color:#fff}
.btn-primary:hover{background:#1976d2}
.alert{display:none;margin-top:10px;padding:10px;border-radius:6px}
.alert-success{background:#d4edda;color:#155724}
.alert-error{background:#f8d7da;color:#721c24}
.small{font-size:12px;color:#666}
</style>
</head>
<body>
<div class="form-change-pass">
    <h2>Đổi mật khẩu</h2>
    <div id="alertBox" class="alert"></div>
    <input type="hidden" id="csrf_token" value="<?=$_SESSION['csrf_token']?>">
    <input type="password" id="old_password" placeholder="Mật khẩu cũ">
    <input type="password" id="new_password" placeholder="Mật khẩu mới (≥ 6 ký tự)">
    <input type="password" id="re_password" placeholder="Nhập lại mật khẩu mới">
    <label class="row">
        <input type="checkbox" id="logout_all" value="1">
        <span class="small">Đăng xuất tất cả thiết bị sau khi đổi</span>
    </label>
    <button class="btn btn-primary" id="btnChangePass">Đổi mật khẩu</button>
</div>

<script>
$("#btnChangePass").on("click", function(){
    $.post("/models/change_password.php", {
        csrf_token: $("#csrf_token").val(),
        old_password: $("#old_password").val(),
        new_password: $("#new_password").val(),
        re_password:  $("#re_password").val(),
        logout_all:   $("#logout_all").is(":checked") ? "1" : "0"
    }, function(res){
        // res: {res, title, content}
        const $a = $("#alertBox").removeClass("alert-success alert-error").show();
        if(res.res === "true"){
            $a.addClass("alert-success").html(res.content);
            $("#old_password, #new_password, #re_password").val('');
            if ($("#logout_all").is(":checked")) {
                // Nếu bạn chọn đăng xuất tất cả: chuyển về trang đăng nhập
                setTimeout(()=> location.href="/views/login.php#login", 800);
            }
        } else {
            $a.addClass("alert-error").html(res.content);
        }
    }, "json").fail(function(){
        const $a = $("#alertBox").removeClass("alert-success").addClass("alert-error").show();
        $a.text("Không thể kết nối máy chủ.");
    });
});
</script>
</body>
</html>
