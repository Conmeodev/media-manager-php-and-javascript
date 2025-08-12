<?php
session_start();

/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/

// Tạo chuỗi ngẫu nhiên (5 ký tự chữ + số)
$captcha_code = substr(str_shuffle("123456789"), 0, 5);
$_SESSION["captcha"] = $captcha_code;

// Kích thước ảnh CAPTCHA
$width = 80;
$height = 40;

// Tạo ảnh
$image = imagecreatetruecolor($width, $height);
$bg_color = imagecolorallocate($image, 255, 255, 255); // Màu nền trắng
$line_color = imagecolorallocate($image, 200, 200, 200); // Màu nhiễu

// Đổ nền
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Thêm nhiễu (đường kẻ)
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $line_color);
}

// Vẽ từng số với màu khác nhau
$x = 10;
for ($i = 0; $i < strlen($captcha_code); $i++) {
    // Tạo màu ngẫu nhiên
    $text_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    
    // Vẽ từng số với màu riêng
    imagestring($image, 5, $x, 10, $captcha_code[$i], $text_color);
    
    // Dịch chuyển vị trí cho số tiếp theo
    $x += 12;
}

// Xuất ảnh
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>
