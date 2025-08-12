<!--/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/ -->
	<footer>
		<div class="copyright"><?php echo $domain; ?> @ Copyright <?php echo date("Y");?></div>
	</footer>
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="<?php echo $http.$domain; ?>/main.js?<?php echo $time;?>"></script>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const element = $('#dropzone');
			if (element.length) {
				_upload();
			}
		});
	</script>
</body>
</html>