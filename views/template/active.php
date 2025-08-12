<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
if (isset($_GET['kichhoat'])) {
    $activationCode = $_GET['kichhoat'];
    $user = _user("_mkh", $activationCode);
    $userEmail = isset($user['_email']) ? $user['_email'] : "hihino";
    $userRecord = _fetch("SELECT * FROM user WHERE _tinhtrang='dakichhoat' and _email='" . $userEmail . "'");
    $userAccount = isset($userRecord['_taikhoan']) ? $userRecord['_taikhoan'] : null;
    
    $thongbao = '<ul>';
    $success = false;
    $ok = 0;

    if (!isset($user['id'])) {
        $thongbao .= "<li>Mã kích hoạt không đúng</li>";
        $ok++;
    } elseif ($userAccount !== $user["_taikhoan"] && isset($userAccount)) {
        $thongbao .= "<li>Email này đã được sử dụng và kích hoạt 1 tài khoản khác.</li>";
        $ok++;
    } elseif ($user['_tinhtrang'] === "dakichhoat") {
        $success = true;
        $thongbao .= "<li>Tài khoản này đã được kích hoạt trước đó.</li>";
        $ok++;
    } elseif ($user['_tinhtrang'] != "chuakichhoat") {
        $success = true;
        $thongbao .= "<li>Trạng thái tài khoản không đúng: ".$user['_tinhtrang']."</li>";
        $ok++;
    } 

    if ($ok === 0) {
        _query("UPDATE user SET _tinhtrang='dakichhoat',_capdo='1' WHERE _mkh='" . $activationCode . "'");
        $thongbao = "<li>Kích hoạt tài khoản thành công, chúc bạn online vui vẻ.</li>";
        auto_login($user['id']);
    }
    $thongbao .= "</ul>";
}
?>
<div class="wrapper">
    <div class="left">
        <div class="container">
            <div class="title" id="active">Xác nhận email</div>
            <?php if (!empty($thongbao)){

                if($ok == 0) {
                    echo "<div class='form-alert succ'>";
                } else {
                    echo "<div class='form-alert fail'>";
                }
                echo "<ul>$thongbao</ul>";
                if($ok == 0) {echo '<a class="btn" href="'.$u_login.'">Đăng nhập ngay</a>';}
                echo "</div>";
            }?>
        </div>
    </div>
    <div class="right">
        <div class="container">
            <div class="title">Phúc lợi đăng nhập</div>
            <div class="menu"><strong>1. </strong>Sử dụng chức năng thêm vào yêu thích</div>
            <div class="menu"><strong>2. </strong>Like, bình luận các video, album, truyện...</div>
            <div class="menu"><strong>3. </strong>Nhận xu hằng ngày</div>
            <div class="menu"><strong>4. </strong>Lưu lịch sử xem video, đọc truyện, xem ảnh</div>
            <div class="menu"><strong>5. </strong>Tạo và quản lý danh sách phát, album cá nhân</div>
            <div class="menu"><strong>6. </strong>Chia sẻ nội dung với bạn bè qua mạng xã hội</div>
            <div class="menu"><strong>7. </strong>Nhận thông báo khi có nội dung mới từ danh sách yêu thích</div>
            <div class="menu"><strong>8. </strong>Tham gia các sự kiện và nhận thưởng đặc biệt</div>
            <div class="menu"><strong>9. </strong>Đăng tải video, truyện, hình ảnh cá nhân</div>
            <div class="menu"><strong>10. </strong>Được ưu tiên trải nghiệm các tính năng mới</div>

        </div>
    </div>
</div>