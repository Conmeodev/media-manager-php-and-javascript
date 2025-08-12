<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
if(!$admin) {header("location:/404.php"); exit();}
$thongbao = null;
if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$ok = 0;
$alert_id = $_GET['id'] ?? null;
$_get = $_GET['act'] ?? null;

if($act="delete") {
	$delete = _query("DELETE FROM `alert` WHERE id='$alert_id'");
	if($delete){
		header("loaction:/");
	}
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$act = $_POST['act'] ?? null;
	if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		$thongbao = "Phiên session không hợp lệ";
	} else {
		unset($_SESSION['csrf_token']);
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
		
		if($act == "addAlert") {
			$__content = $_POST['content'] ?? null;
			$__stt = $_POST['pass'] ?? null;
			$__name = $_POST['name'] ?? null;
			
			if(strlen($__content) <= 0) {
				$thongbao = '';
			} else {
				$insert = _insert("alert",["_stt","_name","_content","_show"],[$__stt,$__name,$__content,'1']);
				if($insert) {
					$thongbao = "Thêm thông báo thành công";
					$ok = 0;
				} else {
					$thongbao = "Thêm thất bại";
				}
			}
		} else if ($act == 'editAlert') {
			$__id = $_POST['id'] ?? null;
			$__content = $_POST['content'] ?? null;
			$__stt = $_POST['stt'] ?? null;
			$__name = $_POST['name'] ?? null;
			
			if (empty($__id) || empty($__content)) {
				$thongbao = "ID hoặc nội dung không được để trống.";
			} else {
				$update = _update("alert", [ "_stt" => $__stt, "_name" => $__name, "_content" => $__content ], ["id" => $__id]);
				if ($update) {
					$thongbao = "Cập nhật thông báo thành công";
					$ok = 0;
				} else {
					$thongbao = "Cập nhật thất bại";
				}
			}
		}

	}
}

?>
<div class="wrapper">
	<div class="left">
		<div class="menu" id="addthongbao">
			<?php if (!empty($thongbao)){
				
				if($ok == 0) {
					echo "<div class='form-alert succ'>";
				} else {
					echo "<div class='form-alert fail'>";
				}
				echo "<ul>$thongbao</ul>";
				echo "</div>";
			}?>
			<?php if($_get =='edit') {?>
			<?php
			$eid = _fetch("SELECT * FROM alert WHERE id='$alert_id'");
			
			?>
			<div class="title">Chỉnh Sửa Thông Báo</div>
					
					<form action="" method="POST"  id="editthongbao">
						<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
						<input type="hidden" name="act" value="editAlert">
						<div class="group-input">
							<label for="id">ID</label>
							<input type="text" name="id" id="id" value="<?php echo $eid['id']; ?>">
						</div>
						<div class="group-input">
							<label for="stt">stt</label>
							<input type="text" name="stt" id="stt" placeholder="Stt..." value="<?php echo $eid['_stt']; ?>">
						</div>
						<div class="group-input">
							<label for="name">Tiêu đề</label>
							<input type="text" name="name" id="name" placeholder="<?php echo $eid['_name']; ?>">
						</div>
						<div class="group-input">
							<textarea name="content" id="content" placeholder="Nội dung thông báo ..."><?php echo $eid['_content']; ?></textarea>
						</div>
						<button>Cập Nhật</button>
					</form>
					<div class="menu"></div>

			<?php }?>
			<div class="title">Thêm Thông Báo</div>
			<form action="" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
				<input type="hidden" name="act" value="addAlert">
				<div class="group-input">
					<label for="stt">stt</label>
					<input type="text" name="stt" id="stt" placeholder="Stt...">
				</div>
				<div class="group-input"> 
					<label for="name">Tiêu đề</label>
					<input type="text" name="name" id="name" placeholder="Tiêu đề...">
				</div>
				<div class="group-input">
					<textarea name="content" id="content" placeholder="Thông báo mới"></textarea>
				</div>
				<button>Thêm</button>
			</form>
		</div>
	</div>
	<div class="right">
		<div class="title">Danh sách thông báo</div>
		<?php
		if($i_alert >0) {
			echo "<div class='notication'>$alert</div>";
		}
		?>
	</div>
</div>

123
12