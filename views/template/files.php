<!--/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/ -->
<?php
$love = "none";
if($user) {
	$_love = _fetch("SELECT * FROM mylove WHERE _uid='".$user['id']."' and _byid='".$inFiles["id"]."'");
	if(isset($_love['id'])) {
		$love = "love";
	}
}
?>
<div class="title menu"><?php echo fPath($inFiles["_byid"]);?></div>
<div class="wrapper flip">
	<div class="left">
		<div class="container">
			
			<?php
			if($admin) {?>
				<div id="dropzone">
					<span>Drop files here or click to upload</span>
					<input class="none" name="path" value="<?php echo $inFiles["_byid"]; ?>">
					<input class="none" name="act" value="upload">
					<input type="file" id="files" name="files[]" multiple style="display: none;">
				</div>
				<div id="progress-container"></div>
			<?php }
			?>
			<div id="#files" class="block">
				<?php
				$count_folders = 0;
				if($admin) {
					$query_folders = _query("SELECT * FROM files WHERE  _byid='".$inFiles['_byid']."' ORDER BY id DESC");
				} else {
					$query_folders = _query("SELECT * FROM files WHERE  _byid='".$inFiles['_byid']."' and _tinhtrang='danghoatdong' ORDER BY id DESC");
				}

				while ($folders = w_fetch($query_folders)) {
					echo boxFiles($folders,"files","ajax");
					$count_folders++;
				}
				?>
			</div>
		</div>
		<?php include_once __DIR__."/post_random.php"; ?>
	</div>
	<div class="right">
		<div class="container">
			<div class="title" id="fileid<?php echo $inFiles['id']; ?>"><h3><?php echo $inFiles['_name']; ?></h3></div>
			
			<div class="view-post">
				<div class="background-view" style="background-image: url(<?php echo $inFiles['_thumb'];?>);"></div>
				<div class="main-view">
					<?php
					if(_tf($inFiles['_type']) == "image") {
					$bbcode = '[img]'.$http.$domain.$inFiles['_dir'].'[/img]';
					?>
						<img src="<?php echo $inFiles["_dir"]; ?>" alt="<?php echo $inFiles['_name']; ?>" loading="lazy">
					<?php } else if(_tf($inFiles['_type']) == "video") {
					    $bbcode = '[video]'.$http.$domain.$inFiles['_dir'].'[/video]';
					?>
						<video id="myVideoView" controls loop autoplay loading="lazy" poster="<?php echo $inFiles['_thumb']; ?>">
							<source src="<?php echo $inFiles["_dir"]; ?>" type="video/mp4">
								Your browser does not support the video tag.
							</video>
						<?php }
						$bbcode .= '[url='.$http.$domain.$inFiles['_dir'].']'.bbcode($inFiles['_name']).'[/url]';
						?>
						
					</div>
				</div>
				<div class="menu morefile">
					<div class="none">
						<input type="text" id="linkcopy" value="<?php echo $url; ?>">
						<input type="text" id="bbcode_copy" value="<?php echo $bbcode; ?>">
					</div>
					<a href="<?php echo $http.$domain.$inFiles['_dir'] ?>" class="btn" target="_blank">Phóng to</a>
					<span onclick="ajaxLove(<?php echo $inFiles['id']; ?>)" id='loveid<?php echo $inFiles['id']?>' class='love btn' love='<?php echo $love; ?>'>Yêu Thích</span>
					<a href="<?php echo $inFiles['_dir'] ?>" class="btn" id="download_file" download>Tải (<span id="fileSize"><?php echo _size($inFiles['_size']); ?></span>)</a>
					<span class="btn" onclick="copy('linkcopy')">Copy Link</span>
					<span class="btn" onclick="copy('bbcode_copy')">Mã nhúng</span>
					<div class="group-input"><input disabled value="<?php echo date("H:i:m d/m/Y",$inFiles['_time']); ?>"></div>
					
				</div>
			</div>
		</div>
	</div>