<?php 
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
?>
<div class="wrapper">
	<div class="right">
		<div class="container">
			<div class="title">Danh sách yêu thích</div>
			<?php
			if(isset($user)) {
				if(!$admin) {
					$get = _query("SELECT * FROM mylove WHERE _uid='".$user['id']."' and _tinhtrang='danghoatdong' ORDER BY id DESC");
				} else {
					$get = _query("SELECT * FROM mylove ORDER BY id DESC");
				}
				echo '<div id="#files" class="block">';
				while($list = w_fetch($get)) {
					$file = _fetch("SELECT * FROM files WHERE id='".$list['_byid']."' and _tinhtrang ='danghoatdong'");
					echo boxFiles($file);
				}
				echo '</div>';
			} else {
				echo "<div class='menu'><span style='font-size: 1.5em;'>Cần Đăng Nhập Để Sử Dụng Tính Năng Này</span></div>";
			}
			?>
		</div>
	</div>
	<div class="left">
		
	</div>
</div>