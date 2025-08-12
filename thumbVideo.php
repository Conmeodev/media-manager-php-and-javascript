<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
// VPS: thumbVideo.php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
$domain = $_SERVER['HTTP_HOST'];
$url = $protocol . "://" . $domain;

$act = $_GET["act"] ?? null;
$linkvideo = $_GET["linkVideo"] ?? null;
$linkThumb = $_GET["linkThumb"] ?? null;

function _thumb_video($linkvideo) {
    global $url;

    // Đảm bảo link video là đường dẫn đầy đủ (HTTP hoặc HTTPS)
    if (!filter_var($linkvideo, FILTER_VALIDATE_URL)) {
        return "URL video không hợp lệ.";
    }

    // Tạo tên file thumbnail
    $outputFile = "create_thumb/" . md5(time() . $linkvideo) . ".png";

    // Command FFmpeg để tạo ảnh thumbnail từ video
    $command = "C:\\ffmpeg\\bin\\ffmpeg.exe -i \"$linkvideo\" -ss 00:00:01.000 -vframes 1 \"$outputFile\"";

    // Thực thi lệnh shell
    shell_exec($command);

    // Kiểm tra nếu thumbnail đã được tạo thành công
    if (file_exists($outputFile)) {
        return $url . '/create_thumb/' . basename($outputFile);
    } else {
        return "Không thể tạo thumbnail từ video.";
    }
}

if ($act == "CREATE_THUMB") {
    echo _thumb_video($linkvideo);  // Tạo và trả về thumbnail của video
} else if ($act == "DELETE_THUMB") {
    echo delete_thumb($linkThumb);  // Xóa thumbnail nếu tồn tại
}
?>
