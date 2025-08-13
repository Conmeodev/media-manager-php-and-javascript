<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
$alert = null;
$file_hientai = [];
include_once __DIR__."/../../_set.php";

if(!$admin){
	_query("INSERT INTO visit(_ip,_url,_time,_type) VALUES('$ip','$history','$time','$history_type')");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta content="index, follow" name="robots"/>
	<meta name="theme-color" content="#42a5f5" />
	<meta name="mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
	<link rel="dns-prefetch" href="https://fonts.googleapis.com">

	<title><?php echo $title; ?></title>
	
	<meta name="description" content="<?php echo $meta_description.' - '.$domain; ?>">
	<meta name="keywords" content="<?php echo $meta_keywords.' - '.$domain; ?>">
	<meta name="author" content="CONMEODEV">
	<link rel="canonical" href="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">

	<!-- Open Graph -->
	<meta property="og:title" content="<?php echo $title; ?>">
	<meta property="og:description" content="<?php echo $meta_description.' - '.$domain; ?>">
	<meta property="og:image" content="<?php echo $meta_thumb; ?>">
	<meta property="og:image:width" content="250" />
	<meta property="og:image:height" content="250" />
	<meta property="og:image:type" content="image/jpeg" />
	<meta property="og:image:alt" content="<?php echo $title; ?>">
	<meta property="og:site_name" content="<?php echo $domain; ?>">
	<meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
	<meta property="og:type" content="website">

	<!-- Twitter Card -->
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?php echo $title; ?>">
	<meta name="twitter:description" content="<?php echo $meta_description.' - '.$domain; ?>">
	<meta name="twitter:image" content="<?php echo $meta_thumb; ?>">

	<!-- DMCA & Other -->
	<meta name="dmca-site-verification" content="dDVBN2oxcUtlcWRZWVNzSDA5MWJLR3VOZU5NTldtbTl1NzBxelZrZ2VGMD01" />
	<meta name="clckd" content="540f7543675fb09f48c3e35050fdadf2" />

	<!-- Stylesheet -->
	<link rel="preload" href="<?php echo $http.$domain; ?>/asset/css/ai-style.css?v=<?php echo time(); ?>" as="style">
	<link rel="stylesheet" href="<?php echo $http.$domain; ?>/asset/css/ai-style.css?v=<?php echo time();?>">
	<link rel="image_src" href="<?php echo $meta_thumb; ?>">
	<link rel="icon" href="<?php echo $http.$domain; ?>/favicon.ico" type="image/x-icon"/>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/spark-md5/3.0.2/spark-md5.min.js"></script>
</head>

<body>
	<header>
		<a href="/" class="logo-text"><?php echo $domain; ?></a>
		<div><h1 style="    word-break: break-word;"><?php echo $title; ?></h1></div>
	</header>
	<div class="none">
		<div id="cf_path"><?php echo $getFolders ?? $inFiles['_byid']; ?></div>
	</div>
	<div class="nav-menu">
		<div class="nav-list">
			<a href="/" class="nav-sub">Trang Chủ</a>
			<a href="<?php echo $u_love; ?>" class="nav-sub">Danh sách yêu thích</a>
			<a href="<?php echo $u_allfile; ?>" class="nav-sub">Tất cả tập tin</a>
			<?php if (!$user): ?>
				<a class="nav-sub" href="<?php echo $u_login; ?>">Đăng Nhập</a>
				<a class="nav-sub" href="<?php echo $u_reg; ?>">Đăng Ký</a>
			<?php else: ?>
				<a class="nav-sub" href="<?php echo $u_change_pass; ?>">Đổi Mật Khẩu</a>
				<a class="nav-sub" href="<?php echo $u_logout; ?>">Đăng Xuất</a>
			<?php endif; ?>
		</div>

	</div>

	<div class="nav-tools menu">

		<form action="<?php echo $u_search; ?>" method="POST" id="search-tools">
			<input type="text" name="keysearch" placeholder="Từ khoá...">
			<button>Tìm</button>
		</form>
	</div>
	<?php
	if($admin) {?>
		<div class="menu">
			<div class="title2">Admin Panel</div>
			<a href="<?php echo $u_alert;?>" class="btn">Quản lý thông Báo</a>
		</div>
	<?php }?>


	<?php

	$i_alert = 0;
	if($admin) {
		$text_alert_header = "SELECT * FROM alert";
	} else {
		$text_alert_header = "SELECT * FROM alert WHERE _show='1'";
	}
	$query_alert_header = _query($text_alert_header." ORDER BY _stt ASC");
	while ($data_alert = w_fetch($query_alert_header)) {

		$alert .= '<div class="alert_box" data-nosnippet>'.$data_alert['_content'];
		if($admin) {
			$alert .= '<a href="'.$u_alert.'?act=edit&id='.$data_alert['id'].'#editthongbao" class="btn">sửa</a>';
			$alert .= '<a href="'.$u_alert.'?act=delete&id='.$data_alert['id'].'#xoa" class="btn" style="background:red" onclick="return confirm(\'Bạn có chắc muốn xoá thông báo này không?\')">xoá</a>';
		}
		$alert .= '</div><hr>';
		$i_alert++;

	}
	if($i_alert >0) {
		echo "<div class='notication'>$alert</div>";
	}
	?>
	<div class="menu">
		<div class="title menu">10 Album mới nhất</div>
		<?php
		$query_new = _query("SELECT * FROM files WHERE _tinhtrang='danghoatdong' and id IN (SELECT MAX(id) FROM files GROUP BY _byid) ORDER BY id DESC LIMIT 10");
		$file_hientai = _fetch("SELECT * FROM files WHERE _tinhtrang='danghoatdong' and id ORDER BY id DESC LIMIT 1")['id'] ?? 0;
		$file_cu = isset($_COOKIE['countFile']) ? $_COOKIE['countFile'] : $file_hientai;
		$_0 = strtotime(date("d-m-Y"));
		$todayCount = _fetch("SELECT COUNT(*) AS total_count FROM files WHERE _tinhtrang='danghoatdong' and _time > $_0")['total_count'];
		?>
		<div id="#files" class=" menu block scroll-x">
			<?php
			while($files = w_fetch($query_new)) {
				echo boxFiles($files);
			}
			?>
		</div>
	</div>
	<div class="menu">
		<div class="title">Mọi người vừa xem</div>
		<div id="#files" class="block scroll-x">
			<?php
			$query_new = _query("SELECT * FROM visit WHERE id IN ( SELECT MAX(id) FROM visit WHERE _url != 0 GROUP BY _url ) and _type='files' ORDER BY id DESC LIMIT 10;");
			while($visit = w_fetch($query_new)) {
				$new_type = $visit['_type'];
				$new_id = $visit['_url'];
				if($admin) {$new_select = _fetch("SELECT * from $new_type WHERE id='$new_id'");}
				else {$new_select = _fetch("SELECT * from $new_type WHERE id='$new_id' and _tinhtrang='danghoatdong'");}

				if($new_type == "folders") {
					echo boxFolders($new_select);
				} else if($new_type == "files"){
					echo boxFiles($new_select);
				}
			}

			?>
		</div>
	</div> 
