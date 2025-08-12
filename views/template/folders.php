<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
if(!$admin) {header("location:/404.php"); exit();}
if (!isset($_SESSION['csrf_token'])) {
	$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
// Kiểm tra CSRF token
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
		$thongbao = "Phiên session không hợp lệ";
	} else {
		unset($_SESSION['csrf_token']);
		$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Nhận dữ liệu từ form
		$id = $_GET['id'] ?? 0;
		$name = trim($_POST['name'] ?? '');
		$thumb = trim($_POST['thumb'] ?? '');
		$content = trim($_POST['content'] ?? '');
		$keysearch = trim($_POST['KeySearch'] ?? '');
		$folder = $_POST['folder'] ?? 0;
		$tinhtrang = $_POST['tinhtrang'] ?? 'danghoatdong';

    // Kiểm tra dữ liệu hợp lệ
		if ($id <= 0 || empty($name)) {
			$thongbao = "<li>Vui lòng nhập tên thư mục hợp lệ!</li>";
		} else {
        // Cập nhật dữ liệu vào database
			$update = _query("
				UPDATE folders SET 
				_name = '"._sql($name)."',
				_thumb = '"._sql($thumb)."',
				_content = '"._sql($content)."',
				keysearch = '"._sql($keysearch)."',
				_byid = '"._sql($folder)."',
				_tinhtrang = '"._sql($tinhtrang)."'
				WHERE id = '"._sql($id)."'
				");

			if ($update) {
				$thongbao = "<li class='success'>Cập nhật thành công!</li>";
			} else {
				$thongbao = "<li class='fail'>Có lỗi xảy ra khi cập nhật!</li>";
			}
		}
	}
}

$act = $_GET['act'] ?? null;
if(if5($act, "panelFolders")) {
	$id = $_GET['id'] ?? 0;
	$folders = getFolders($id);
	?>
	<div class="wrapper">
		<div class="left">
			<form action="" class="container" method="POST">
				<input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
				<div class="title-form" id="login">Chỉnh sửa thư mục</div>
				<?php if (!empty($thongbao)) {
					echo "<div class='form-alert fail'><ul>$thongbao</ul></div>";
				}?>
				<div id="#efolders" class="list redit">
					<?php echo boxFolders($folders); ?>
				</div>
				<div class="group-input">
					<label for="name">Đổi Tên</label>
					<input type="text" id="name" name="name" value="<?php echo htmlspecialchars($folders["_name"]); ?>">
				</div>
				<div class="group-input">
					<label for="thumb">Thumb</label>
					<input type="text" id="thumb" name="thumb" value="<?php echo htmlspecialchars($folders["_thumb"]); ?>">
				</div>
				<div class="group-input">
					<label for="content">Mô Tả</label>
					<textarea id="content" name="content"><?php echo htmlspecialchars($folders["_content"]); ?></textarea>
				</div>
				<div class="group-input">
					<label for="KeySearch">KeySearch</label>
					<input type="text" id="KeySearch" name="KeySearch" value="<?php echo htmlspecialchars($folders["keysearch"]); ?>">
				</div>
				<div class="group-input">
					<label for="sub">Di Chuyển</label>
					<?php
					echo '<select name="folder" class="menu">';
					echo '<option value="'.$folders['_byid'].'">'.$folders['_byid'].'. GIỮ NGUYÊN</option>';
					echo '<option value="0">- Thư mục gốc -</option>';
					echo getFolderOptions();
					echo '</select>';
					?>
				</div>
				<div class="group-input">
					<label for="tinhtrang">Tình Trạng</label>
					<select name="tinhtrang" id="tinhtrang" class="menu">
						<option value="<?php echo $folders['_tinhtrang']; ?>"><?php echo $folders['_tinhtrang']; ?></option>
						<option value="danghoatdong">Hoạt Động</option>
						<option value="daxoa">Ẩn</option>
					</select>
				</div>

				<input type="submit" value="Cập nhật">
			</form>
		</div>

		<div class="right">
			<div class="container">
				<div class="title">Thư mục liên kết</div>
				<div id="#folders" class="list">
					<?php
					$query_folders = _query("SELECT * FROM folders WHERE _byid='$id' AND _tinhtrang='danghoatdong' ORDER BY id DESC");
					while ($folders = w_fetch($query_folders)) {
						echo boxFolders($folders);
					}
					?>
				</div>
			</div>
			
		</div>
	</div>
	<script>
		let timeout;

		const nameInput = document.getElementById('name');
		const thumbInput = document.getElementById('thumb');
		const contentInput = document.getElementById('content');
		const keySearchInput = document.getElementById('KeySearch');
		const tinhTrangInput = document.getElementById('tinhtrang');
		const foldersBox = document.getElementById('#efolders');

		function updateFoldersBox() {
			const name = nameInput.value;
			const thumb = thumbInput.value;
			const content = contentInput.value;
			const keySearch = keySearchInput.value;
			const tinhTrang = tinhTrangInput.value;
			const folderItem = foldersBox.querySelector('.box');
			if (folderItem) {
				folderItem.setAttribute('_name', name);
				//folderItem.setAttribute('_thumb', thumb);
				$(folderItem).find(".ithumb").attr("src", thumb);
				
				folderItem.setAttribute('_content', content);
				folderItem.setAttribute('keysearch', keySearch);
				folderItem.querySelector('.list-name').textContent = name;
				//folderItem.querySelector('.list-time').textContent = new Date().toLocaleString();
				//folderItem.querySelector('.tinhtrang').textContent = tinhTrang;
			}
		}

		[nameInput, thumbInput, contentInput, keySearchInput, tinhTrangInput].forEach(input => {
			input.addEventListener('input', function() {
				clearTimeout(timeout);
				timeout = setTimeout(updateFoldersBox, 500);
			});
		});

	</script>
<?php } ?>
