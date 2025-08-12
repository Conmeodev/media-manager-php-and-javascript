<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
if($user) {
    exit(header("Location:/"));
    
}
if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$taikhoan = $_POST['user'] ?? '';
	$matkhau = $_POST['pass'] ?? '';

	$result = login($taikhoan, $matkhau);
	if ($result === true) {
		echo "Đăng nhập thành công!";
		exit(header("Location:/"));
		//exit();
	} else {
		$thongbao = $result;
	}
}
?>
<div class="wrapper">
	<div class="left">
		<form action="" class="container" method="POST">
			<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
			<div class="title-form" id="login">ĐĂNG NHẬP</div>
			<?php if (!empty($thongbao)){
				echo "<div class='form-alert fail'><ul>$thongbao</ul></div>";
			}?>

			<div class="group-input">
				<label for="user">Tài Khoản</label>
				<input type="text" id="user" name="user" placeholder="Tài Khoản...">
			</div>
			<div class="group-input">
				<label for="pass">Mật khẩu:</label>
				<input type="password" id="pass" name="pass" placeholder="Mật khẩu...">
			</div>
			<input type="submit">
		</form>
	</div>


	<div class="right">
		<div class="container">
			<div class="title">Phúc lợi đăng nhập</div>
			<div class="menu"><strong>Hi! </strong>Hiện tại khi đăng ký, anh em có thể thêm video vào danh sách yêu thích. Nhận email từ <?php echo $domain ?> mỗi khi có các website  mới hoặc group telegram mới nếu các kênh hiện tại bị sập.</div>
			<?php /*
			<div class="menu"><strong>1. </strong>Sử dụng chức năng thêm vào yêu thích</div>
			<div class="menu"><strong>2. </strong>Like, bình luận các video, album, truyện...</div>
			<div class="menu"><strong>3. </strong>Nhận xu hằng ngày</div>
			<div class="menu"><strong>4. </strong>Lưu lịch sử xem video, đọc truyện, xem ảnh</div>
			<div class="menu"><strong>5. </strong>Tạo và quản lý danh sách phát, album cá nhân</div>
			<div class="menu"><strong>6. </strong>Chia sẻ nội dung với bạn bè qua mạng xã hội</div>
			<div class="menu"><strong>7. </strong>Nhận thông báo khi có nội dung mới từ danh sách yêu thích</div>
			<div class="menu"><strong>8. </strong>Tham gia các sự kiện và nhận thưởng đặc biệt</div>
			<div class="menu"><strong>9. </strong>Đăng tải video, truyện, hình ảnh cá nhân</div>
			<div class="menu"><strong>10. </strong>Được ưu tiên trải nghiệm các tính năng mới</div>*/?>
		</div>
	</div>
</div>