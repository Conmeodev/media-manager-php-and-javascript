<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
if($user) {header("location:/");exit();}
if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$thongbao = null;
	$_captcha = $_SESSION["captcha"];
	if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		header("location:$u_reg");
	}
	unset($_SESSION['csrf_token']);
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
	
	$captcha = $_POST['captcha'] ?? null;

	$ok = 0;

	if($captcha != $_captcha) {
		$ok++;
		$thongbao = "<li>Bạn nhập sai captcha.</li>";
	} else {
		$__user = $_POST['user'] ?? null;
		$__pass = $_POST['pass'] ?? null;
		$__email = $_POST['email'] ?? null;

		$_user = _sql($__user);
		$_pass = _sql($__pass);
		$_email = _sql($__email);
		if(strlen($_user) < 2) {
			$thongbao .= "<li>Tài khoản phải trên 2 kí tự.</li>";
			$ok++;
		} else if(_user("_taikhoan",$_user) != null) {
			$thongbao .= "<li>Tài khoản này đã tồn tại.</li>";
			$ok++;
		} else if(is_ktdb($_user)){
			$thongbao .= "<li>Tài khoản không được chứa kí tự đặc biệt.</li>";
			$ok++;
		}
		if(strlen($_pass) < 1) {
			$thongbao .= "<li>Mật khẩu phải trên 1 kí tự.</li>";
			$ok++;
		}
		if(checkmail($_email)) {
			$thongbao .= "<li>Định dạng email không đúng.</li>";
			$ok++;
		} else if(_user("_email",$_email) != null) {
			if(_user("_email",$_email)['_tinhtrang'] == 'dakichhoat') {
				$thongbao .= "<li>Email này đã kích hoạt cho tài khoản khác.</li>";
				$ok++;
			}
		}

		if($ok == 0){
			$title = "Thành công";
			$user = _sql($_user);
			$pass = _sql($_pass);
			$email = _sql($_email);
			$mkh = hash("sha256",$user.$pass.$email.$time.$ip).rand(1000,9999);


			//$insert = _query("INSERT INTO user(_taikhoan,_matkhau,_email,_ip,_tinhtrang,_time,_mkh,_cap) VALUES('$user','$pass','$email','$ip','chuakichhoat','$time','$mkh','cap')");
			$insert = _insert("user", 
				["_taikhoan", "_matkhau", "_email", "_ip", "_tinhtrang", "_time", "_mkh", "_cap"], 
				[$user, $pass, $email, $ip, "chuakichhoat", $time, $mkh, "cap"]
			);
			
			if($insert) {
				$res = "true";
				$title = "Thành công";
				$thongbao = "<ul><h1>Đăng ký thành công</h1><hr>
				<li>- Tài khoản: $user</li>
				<li>- Mật khẩu: $pass</li>
				<li>- Email: $email</li>
				<li>* Vui lòng kiểm tra email, dể xác minh và hoàn tất đăng ký.</li></ul><br>
				";
				$thongbao_mail = 
				<<<MAIL
				<div style="max-width: 600px; margin: 0 auto; padding: 20px; background-color:#fff"> 
				<h2>Xác nhận tài khoản của bạn</h2>
				<p>Xin chào <b>$_user</b>, đây là thư xác nhận đăng ký tài khoản tại <b>$domain</b>. Vui lòng nhấn vào nút bên dưới để kích hoạt tài khoản của bạn. </p>
				<a href="$http$domain$u_active?kichhoat=$mkh#active" style="background-color: #4CAF50; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px; margin: 4px 2px; cursor: pointer;">Kích hoạt <b>$_user</b></a>
				<br>
				<ul><strong>Thông tin đăng nhập:</strong>
				<li>Tài khoản: $user</li>
				<li>Mật khẩu: $pass</li>
				<li>Email: $email</li>
				<li>Bạn có thể sử dụng tài khoản này cho tất cả website có trong hệ thống $domain của chúng tôi.</li>
				</ul>
				<br>
				<hr>
				<br>
				* Nếu bạn không đăng ký vui lòng bỏ qua email này.
				</div>
MAIL;
				sendMail('Xác minh tài khoản',$thongbao_mail,$domain,$email);
			}

		}

	}


}
?>
<div class="wrapper">
	<div class="left">
		<form action="" class="container" method="POST">
			<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
			<div class="title-form" id="reg">ĐĂNG KÝ</div>
			<?php if (!empty($thongbao)){
				
				if($ok == 0) {
					echo "<div class='form-alert succ'>";
				} else {
					echo "<div class='form-alert fail'>";
				}
				echo "<ul>$thongbao</ul>";
				echo "</div>";
			}?>

			<div class="group-input">
				<label for="user">Tài Khoản</label>
				<input type="text" id="user" name="user" placeholder="Tài Khoản...">
			</div>
			<div class="group-input">
				<label for="pass">Mật khẩu:</label>
				<input type="text" id="pass" name="pass" placeholder="Mật khẩu...">
			</div>
			<div class="group-input">
				<label for="email">Email:</label>
				<input type="text" id="email" name="email" placeholder="@">
			</div>
			<div class="group-input">
				<label for="captcha">Nhập lại số sau: <img src="/asset/image/captcha.php" alt=""></label>
				<input type="number" id="captcha" name="captcha" placeholder="Xác nhận không phải robot">
			</div>
			<input type="submit">
		</form>
	</div>
	<div class="right">
		<div class="container">
			<div class="title">Điều Khoản & Chính Sách</div>
			<div class="menu"><STRONG>Khi sử dụng trang web của chúng tôi, bạn đồng ý với tất cả các điều khoản và điều kiện dưới đây. Nếu bạn không đồng ý, vui lòng ngừng sử dụng dịch vụ.</STRONG></div>
			<div class="menu"><strong>1. Quyền Sở Hữu và Nội Dung</strong>
			Tất cả nội dung trên trang web, bao gồm nhưng không giới hạn ở video, hình ảnh, văn bản và các tài nguyên khác, đều thuộc quyền sở hữu của các tác giả hoặc nhà cung cấp nội dung. Trang web không chịu trách nhiệm đối với việc vi phạm bản quyền hoặc các vi phạm pháp lý từ người dùng khi tải lên hoặc chia sẻ nội dung.</div>
			<div class="menu">
				<strong>2. Trách Nhiệm Người Dùng</strong>
				Người dùng có trách nhiệm đảm bảo rằng tất cả nội dung tải lên, chia sẻ hoặc xem trên trang web không vi phạm bản quyền, quyền sở hữu trí tuệ hoặc các quyền hợp pháp khác của bất kỳ cá nhân hoặc tổ chức nào.
			</div>
			<div class="menu">
				<strong>3.Sử Dụng Dịch Vụ</strong>
				Bạn đồng ý sử dụng dịch vụ của chúng tôi chỉ cho mục đích hợp pháp. Bạn không được phép sử dụng dịch vụ để tải lên, chia sẻ hoặc phân phối bất kỳ nội dung nào vi phạm pháp luật, mang tính chất đe dọa, xúc phạm, lừa đảo hoặc gây hại cho người khác.
			</div>
			<div class="menu">
				<strong>4. Chính Sách Bảo Mật</strong>
				Trang web của chúng tôi cam kết bảo vệ quyền riêng tư của người dùng. Mọi thông tin cá nhân của bạn sẽ chỉ được sử dụng cho các mục đích hợp pháp theo chính sách bảo mật của chúng tôi và sẽ không được tiết lộ cho bên thứ ba mà không có sự đồng ý của bạn, trừ khi có yêu cầu từ pháp luật.
			</div>
			<div class="menu">
				<strong>5. Miễn Trách Nhiệm</strong>
				Trang web không chịu bất kỳ trách nhiệm nào đối với các thiệt hại trực tiếp hoặc gián tiếp, bao gồm nhưng không giới hạn ở việc mất dữ liệu hoặc sự gián đoạn trong việc sử dụng dịch vụ, do việc tải lên, chia sẻ hoặc xem nội dung trên trang web. Người dùng hoàn toàn chịu trách nhiệm về nội dung của mình.

			</div>
			<div class="menu">
				<strong>6. Thay Đổi Điều Khoản</strong>
				Chúng tôi có quyền thay đổi hoặc cập nhật các điều khoản và điều kiện này vào bất kỳ lúc nào mà không cần thông báo trước. Việc tiếp tục sử dụng dịch vụ sau khi có thay đổi đồng nghĩa với việc bạn chấp nhận các điều khoản mới.

			</div>
			<div class="menu">
				<strong>7. Hạn Chế Trách Nhiệm</strong>
				Trang web không chịu trách nhiệm đối với bất kỳ hành vi vi phạm pháp lý, tổn thất hoặc thiệt hại do việc sử dụng dịch vụ hoặc việc tải xuống nội dung từ trang web, bao gồm các video và tài nguyên được cung cấp.
			</div>
			<div class="menu">
				<strong>8. Giải Quyết Tranh Chấp</strong>
				Mọi tranh chấp phát sinh từ việc sử dụng dịch vụ của chúng tôi sẽ được giải quyết theo quy định của pháp luật Việt Nam. Các bên đồng ý giải quyết tranh chấp tại tòa án có thẩm quyền tại Việt Nam.
			</div>
			<div class="menu">
				<strong>9. Giới Hạn Trách Nhiệm</strong>
				Trang web của chúng tôi không chịu trách nhiệm đối với bất kỳ thiệt hại nào do các lỗi kỹ thuật, gián đoạn dịch vụ, hoặc truy cập không hợp lệ bởi các bên thứ ba.
			</div>
			<div class="menu">
				<strong>10. Hành Vi Phá Hoại Cố Tình</strong>
				Chúng tôi nghiêm cấm mọi hành vi phá hoại hoặc tấn công vào hệ thống, bao gồm nhưng không giới hạn ở việc xâm nhập trái phép vào dữ liệu, gây ra sự gián đoạn, hoặc làm hỏng các dịch vụ của trang web. Người dùng không được phép sử dụng dịch vụ của chúng tôi để thực hiện các hành vi phá hoại, tấn công từ chối dịch vụ (DDoS), lây lan mã độc hoặc virus, hoặc gây bất kỳ thiệt hại nào cho hệ thống của trang web.
			</div>

			<div class="menu">
				<strong> 11. Cấm Thực Hiện Tấn Công và Phá Hoại</strong>
				Bất kỳ hành động tấn công, cố tình làm giảm chất lượng dịch vụ, hoặc phá hoại chức năng của trang web hoặc hệ thống đều là hành vi vi phạm nghiêm trọng và sẽ bị xử lý theo pháp luật, bao gồm việc truy cứu trách nhiệm hình sự nếu cần thiết.
			</div>

			<div class="menu">
				<strong>12. Cấm Spam và Hành Vi Gửi Tin Nhắn Không Mong Muốn</strong>
				Chúng tôi nghiêm cấm mọi hành vi spam, bao gồm nhưng không giới hạn ở việc gửi tin nhắn, email, hoặc thông báo không mong muốn, không có sự đồng ý từ người nhận, hoặc quảng cáo mà không được phép. Điều này bao gồm việc gửi thông tin quảng cáo, khuyến mãi, hoặc bất kỳ nội dung nào mang tính chất spam qua hệ thống của chúng tôi.
			</div>

			<div class="menu">
				<i>
					Chúng tôi có quyền ngừng, đình chỉ hoặc xóa tài khoản của bất kỳ người dùng nào nếu vi phạm chính sách của chúng tôi mà không cần thông báo trước.
				</i>
			</div>
		</div>
	</div>
</div>