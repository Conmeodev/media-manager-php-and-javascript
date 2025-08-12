<!--
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
-->
<div class="wrapper">
	<div class="right">
		<div class="container">
			<?php
			$callFiles = _query("SELECT * FROM files WHERE _tinhtrang='danghoatdong' ORDER BY id DESC");

			$filesByDate = [];
			while ($files = w_fetch($callFiles)) {
				$date = date("d/m/Y", $files['_time']);
				if (!isset($filesByDate[$date])) {
					$filesByDate[$date] = [];
				}
				$filesByDate[$date][] = $files;
			}

			if (!empty($filesByDate)) {
				foreach ($filesByDate as $date => $files) {
					echo '<div class="title">Upload ngày ' . $date . '</div>';
					echo '<div id="files" class="block">';
					foreach ($files as $file) {
						echo boxFiles($file);
					}
					echo '</div>';
				}
			}
			?>
		</div>
	</div>
	<div class="left">
	</div>
</div>
