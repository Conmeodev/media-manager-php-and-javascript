<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
include_once $_SERVER['DOCUMENT_ROOT'] . "/_set.php";
if (!$user) { header("Location: /views/login.php#login"); exit; }

// Tạo CSRF token nếu chưa có
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<div class="wrapper">
    <div class="left">
        <div class="container form-change-pass" >
            <input type="hidden" id="csrf_token" value="<?=$_SESSION['csrf_token']?>">
            <div class="title-form" id="reg">ĐỔI MẬT KHẨU</div>
            <div id="alertBox" class="alert"></div>

            <div class="group-input">
                <label for="old_password">Mật khẩu cũ:</label>
                <input type="text" id="old_password" name="old_password" placeholder="Mật khẩu cũ...">
            </div>
            <div class="group-input">
                <label for="new_password">Mật khẩu mới:</label>
                <input type="text" id="new_password" name="new_password" placeholder="Mật khẩu mới...">
            </div>
            <div class="group-input">
                <label for="re_password">Nhập lại mật khẩu mới:</label>
                <input type="text" id="re_password" name="re_password" placeholder="nhập lại mật khẩu...">
            </div>
            <div class="group-input checkbox" style="">
                <input type="checkbox" id="logout_all" value="1">
                <span class="small">Đăng xuất tất cả thiết bị sau khi đổi</span>
            </div>
            <button class="btn btn-primary" id="btnChangePass">Đổi mật khẩu</button>
        </div>
    </div>
    <div class="right">

    </div>
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
        const $a = $("#alertBox").removeClass("succ fail").show();
        if(res.res === "true"){
            $a.addClass("succ").html(res.content);
            $("#old_password, #new_password, #re_password").val('');
            if ($("#logout_all").is(":checked")) {
                // Nếu bạn chọn đăng xuất tất cả: chuyển về trang đăng nhập
                setTimeout(()=> location.href="/views/login.php#login", 800);
            }
        } else {
            $a.addClass("fail").html(res.content);
        }
    }, "json").fail(function(){
        const $a = $("#alertBox").removeClass("succ").addClass("fail").show();
        $a.text("Không thể kết nối máy chủ.");
    });
});
</script>
