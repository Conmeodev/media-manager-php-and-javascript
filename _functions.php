<?php
include "PHPMailer/src/PHPMailer.php";
include "PHPMailer/src/Exception.php";

include "PHPMailer/src/OAuthTokenProvider.php";
include "PHPMailer/src/OAuth.php";
include "PHPMailer/src/POP3.php";
include "PHPMailer/src/SMTP.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
function is_url($url) {
	return filter_var($url, FILTER_VALIDATE_URL) !== false;
}
function if5($text,$text2){
	if(md5($text) == md5($text2)) {
		return true;
	} else {
		return false;
	}
}
function is_ktdb($chuoi) {
	$pattern = '/[^A-Za-z0-9_]/';
	return preg_match($pattern, $chuoi);
}
function checkmail($txt){
	$partten = "/^[A-Za-z0-9_.]{1,32}@([a-zA-Z0-9]{1,12})(.[a-zA-Z]{1,12})+$/";
	$subject = $txt;
	if(!preg_match($partten ,$subject, $matchs)) {
		return true;
	} else {
		return false;
	}
}
function sendMail($title, $content, $tennguoigui, $emailnguoinhan,$diachicc=''){
    GLOBAL $mailEmail, $mailPass;
	$nFrom = $tennguoigui;
	$mFrom = $mailEmail;   
	$mPass = $mailPass;       
	$mail             = new PHPMailer();
	$body             = $content;
	$mail->IsSMTP(); 
	$mail->CharSet   = "utf-8";
	$mail->SMTPDebug  = 0;                    
	$mail->SMTPAuth   = true;                    
	$mail->SMTPSecure = "ssl";                
	$mail->Host       = "smtp.gmail.com";        
	$mail->Port       = 465;
	$mail->Username   = $mFrom;  
	$mail->Password   = $mPass;               
	$mail->SetFrom($mFrom, $nFrom);
	$ccmail = explode(',', $diachicc);
	$ccmail = array_filter($ccmail);
	if(!empty($ccmail)){
		foreach ($ccmail as $k => $v) {
			$mail->AddCC($v);
		}
	}
	$mail->Subject    = $title;
	$mail->MsgHTML($body);
	$address = $emailnguoinhan;
	$mail->AddAddress($address, $tennguoigui);
	$mail->AddReplyTo($mailEmail, $tennguoigui);
	if(!$mail->Send()) {
		return 0;
	} else {
		return 1;
	}
}
function _get($url) {
    GLOBAL $http, $domain;
    $fullUrl = $http . $domain . $url;

    // Tạo tệp tạm thời để lưu cookie
    $cookieFile = tempnam(sys_get_temp_dir(), 'cookie');

    // Lấy tất cả cookie hiện tại của người dùng
    $cookies = [];
    foreach ($_COOKIE as $key => $value) {
        $cookies[] = $key . '=' . $value;
    }
    $cookieString = implode('; ', $cookies); // Tạo chuỗi cookie

    // Khởi tạo cURL
    $ch = curl_init();

    // Cấu hình cURL
    curl_setopt($ch, CURLOPT_URL, $fullUrl); // URL đầy đủ
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Lấy dữ liệu trả về
    curl_setopt($ch, CURLOPT_COOKIE, $cookieString); // Gửi cookie cùng request
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Theo dõi chuyển hướng (nếu có)

    // Thực hiện yêu cầu và lấy nội dung trả về
    $response = curl_exec($ch);

    // Kiểm tra lỗi
    if (curl_errno($ch)) {
        $error_msg = 'CURL Error: ' . curl_error($ch);
        curl_close($ch);
        unlink($cookieFile); // Xóa tệp cookie sau khi hoàn tất
        return $error_msg;
    }

    curl_close($ch);
    unlink($cookieFile); // Xóa tệp cookie sau khi hoàn tất

    return $response; // Trả về nội dung của trang
}

function _get1($url) {
	GLOBAL $http,$domain;
    return file_get_contents($http.$domain.$url);
}
function bodau($str){
    $unicode = array(
        'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
        'd'=>'đ',
        'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'i'=>'í|ì|ỉ|ĩ|ị',
        'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
        'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'D'=>'Đ',
        'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
        'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        ' '=> '_',
    );
    foreach($unicode as $nonUnicode=>$uni){
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }
    $str = str_replace(' ','_',$str);
    return $str;
}
function ktdb($string) {
    $replace_cham = str_replace('.', 'dauchamkaka', $string);
    $string0 = str_replace(' ', '-', $replace_cham);
    $string1 = preg_replace('/[^A-Za-z0-9_]/', '', $string0);
    return str_replace('dauchamkaka', '.', $string1);
}

function cvLink($string) {
    $string = mb_strtolower($string, 'UTF-8');
    $vietnameseMap = array(
        'à' => 'a', 'á' => 'a', 'ạ' => 'a', 'ả' => 'a', 'ã' => 'a', 
        'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ậ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a',
        'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ặ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a',
        'è' => 'e', 'é' => 'e', 'ẹ' => 'e', 'ẻ' => 'e', 'ẽ' => 'e',
        'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ệ' => 'e', 'ể' => 'e', 'ễ' => 'e',
        'ì' => 'i', 'í' => 'i', 'ị' => 'i', 'ỉ' => 'i', 'ĩ' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ọ' => 'o', 'ỏ' => 'o', 'õ' => 'o',
        'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ộ' => 'o', 'ổ' => 'o', 'ỗ' => 'o',
        'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ợ' => 'o', 'ở' => 'o', 'ỡ' => 'o',
        'ù' => 'u', 'ú' => 'u', 'ụ' => 'u', 'ủ' => 'u', 'ũ' => 'u',
        'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ự' => 'u', 'ử' => 'u', 'ữ' => 'u',
        'ỳ' => 'y', 'ý' => 'y', 'ỵ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y',
        'đ' => 'd',
    );

    $string = strtr($string, $vietnameseMap);
    $string = preg_replace('/[^a-z0-9]+/i', '-', $string);
    $string = trim($string, '-');

    return $string;
}
function randCol() {
    $red = rand(0, 255);
    $green = rand(0, 255);
    $blue = rand(0, 255);
    return sprintf('#%02X%02X%02X', $red, $green, $blue);
}
function bbcode($text) {
    GLOBAL $domain;
    $text = htmlspecialchars($text);
    $text = preg_replace_callback('/\[html\](.*?)\[\/html\]/s', function($matches) {
        return htmlspecialchars_decode($matches[1]);
    }, $text);
    $search = array(
        '~\[hr=(.*?)\]~s',
        '~\[color=(.*?)\](.*?)\[/color\]~s',
        '~\[div=(.*?)\](.*?)\[/div\]~s',
        '~\[b\](.+?)\[/b\]~s',
        '~\[url\](.+?)\[/url\]~s',
        '~\[url=(.+?)\](.+?)\[/url\]~s',
        '~\[img\](.+?)\[/img\]~s',
    );
    $replace = array(
        '<span style="background:$1;display:block;width:100%;height:1px;margin-top: 11px;"></span>',
        '<span style="color:$1">$2</span>',
        '<div style="$1">$2</div>',
        '<strong>$1</strong>',
        '<a href="$1">$1</a>',
        '<a href="$1">$2</a>',
        '<img src="$1">',
    );

    return preg_replace($search, $replace, $text);
}
function delBBCODE($text) {
    // Loại bỏ các mã BBCode không cần thiết
    $search = array(
        '~\[html\](.*?)\[/html\]~s',
        '~\[hr=(.*?)\]~s',
        '~\[color=(.*?)\](.*?)\[/color\]~s',
        '~\[div=(.*?)\](.*?)\[/div\]~s',
        '~\[b\](.*?)\[/b\]~s',
        '~\[url\](.*?)\[/url\]~s',
        '~\[url=(.*?)\](.*?)\[/url\]~s',
        '~\[img\](.*?)\[/img\]~s',
    );

    // Thay thế tất cả BBCode bằng chuỗi rỗng để loại bỏ chúng
    return preg_replace($search, '', $text);
}
function urlNoQuery() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $domain = $_SERVER['HTTP_HOST'];
    $path = strtok($_SERVER["REQUEST_URI"], '?');
    return $protocol . $domain . $path;
}

function resizeImage($maxWidth, $sourceImage, $outputDir) {
    // Lấy kích thước ảnh gốc
    list($originalWidth, $originalHeight) = getimagesize($sourceImage);

    // Kiểm tra nếu ảnh nhỏ hơn maxWidth thì không resize
    if ($originalWidth <= $maxWidth) {
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;
    } else {
        // Tính toán kích thước mới dựa trên maxWidth và giữ nguyên tỉ lệ ảnh
        $newWidth = $maxWidth;
        $newHeight = ($originalHeight / $originalWidth) * $newWidth;
    }

    // Tạo đối tượng ảnh mới với kích thước đã tính
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Tạo ảnh từ file gốc
    $source = imagecreatefromjpeg($sourceImage);

    // Thay đổi kích thước ảnh mà không làm thay đổi tỉ lệ
    imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

    // Tạo đường dẫn file mới
    $outputFile = $outputDir . '/resized_' . basename($sourceImage);

    // Lưu ảnh đã thay đổi kích thước vào thư mục đích
    imagejpeg($newImage, $outputFile, 90);  // 90 là chất lượng nén

    // Giải phóng bộ nhớ
    imagedestroy($source);
    imagedestroy($newImage);

    // Trả về đường dẫn ảnh đã thay đổi kích thước
    return $outputFile;
}
function _videoThumb($path, $token, $file) {
    global $root;
    $xroot = "$root/$path/$token.png"; // Đường dẫn file vật lý
    $database = "$path/$token.png";    // Đường dẫn lưu vào database

    // Kiểm tra và tạo thư mục nếu chưa tồn tại
    if (!is_dir($root."/".$path)) {
        if (!mkdir($root."/".$path, 0755, true)) {
            echo "Error: Cannot create directory $root/$path.";
            return false;
        }
    }

    // Kiểm tra và xử lý base64
    if (strpos($file, 'data:image') !== false) {
        $file = preg_replace('#^data:image/\w+;base64,#i', '', $file);
        $file = str_replace(' ', '+', $file); // Thay thế khoảng trắng bằng '+' nếu có
    }

    // Giải mã base64
    $decodedData = base64_decode($file);
    if ($decodedData === false) {
        echo "Error: Base64 decoding failed.";
        return false;
    }

    // Tạo hình ảnh từ dữ liệu đã giải mã
    $sourceImage = imagecreatefromstring($decodedData);
    if ($sourceImage === false) {
        echo "Error: Unable to create image from decoded data.";
        return false;
    }

    // Kiểm tra và giảm kích thước nếu cần thiết
    $maxSize = 100 * 1024; // 100KB
    ob_start();
    imagepng($sourceImage);
    $imageData = ob_get_contents();
    ob_end_clean();

    $imageSize = strlen($imageData);

    if ($imageSize > $maxSize) {
        $scaleFactor = sqrt($maxSize / $imageSize);
        $newWidth = max(1, (int)(imagesx($sourceImage) * $scaleFactor)); // Đảm bảo không nhỏ hơn 1
        $newHeight = max(1, (int)(imagesy($sourceImage) * $scaleFactor));

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        if ($resizedImage === false) {
            echo "Error: Failed to create resized image.";
            imagedestroy($sourceImage);
            return false;
        }

        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);

        if (!imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, imagesx($sourceImage), imagesy($sourceImage))) {
            echo "Error: Failed to resize image.";
            imagedestroy($sourceImage);
            imagedestroy($resizedImage);
            return false;
        }

        // Lưu ảnh đã resize
        if (!imagepng($resizedImage, $xroot)) {
            echo "Error: Failed to save resized image to $xroot.";
            imagedestroy($sourceImage);
            imagedestroy($resizedImage);
            return false;
        }

        imagedestroy($resizedImage);
    } else {
        // Lưu ảnh gốc nếu nhỏ hơn 100KB
        if (!imagepng($sourceImage, $xroot)) {
            echo "Error: Failed to save original image to $xroot.";
            imagedestroy($sourceImage);
            return false;
        }
    }

    imagedestroy($sourceImage);

    // Cập nhật database an toàn
    $databasePath = _sql("/$database");
    $safeToken = _sql($token);
    $updateQuery = "UPDATE files SET _thumb='$databasePath' WHERE _token='$safeToken'";
    if (_query($updateQuery)) {
        echo "Thumbnail successfully created and database updated: $databasePath";
        return true;
    } else {
        echo "Error: Failed to update database.";
        return false;
    }
}

function _imageThumb($kichThuocToiDa, $anhGoc, $tepDauRa) {
    $startTime = microtime(true);
    
    // 1. Tối ưu tải ảnh từ URL
    if (filter_var($anhGoc, FILTER_VALIDATE_URL)) {
        $context = stream_context_create([
            'http' => ['timeout' => 5] // Timeout sau 5 giây
        ]);
        $anhGoc = file_get_contents($anhGoc, false, $context);
        if ($anhGoc === false) return "Error: Could not download image";
        
        $tmpFile = sys_get_temp_dir().'/temp_'.md5(microtime()).'.tmp';
        if (!file_put_contents($tmpFile, $anhGoc)) return "Error: Could not save temp file";
        $anhGoc = $tmpFile;
    }

    // 2. Kiểm tra file nhanh hơn
    if (!is_readable($anhGoc)) return "Error: Image not readable";

    // 3. Tối ưu đọc thông tin ảnh
    $info = @getimagesize($anhGoc);
    if ($info === false) return "Error: Invalid image";
    list($width, $height, $type) = $info;

    // 4. Giới hạn kích thước ảnh tối đa
    $MAX_DIMENSION = 3000; // Ngăn xử lý ảnh quá lớn
    if ($width > $MAX_DIMENSION || $height > $MAX_DIMENSION) {
        $ratio = min($MAX_DIMENSION/$width, $MAX_DIMENSION/$height);
        $width = (int)($width * $ratio);
        $height = (int)($height * $ratio);
    }

    // 5. Tạo ảnh từ file với bộ nhớ tối ưu
    ini_set('memory_limit', '256M'); // Tăng memory limit tạm thời
    switch ($type) {
        case IMAGETYPE_JPEG: $source = @imagecreatefromjpeg($anhGoc); break;
        case IMAGETYPE_PNG: $source = @imagecreatefrompng($anhGoc); break;
        case IMAGETYPE_GIF: $source = @imagecreatefromgif($anhGoc); break;
        default: return "Error: Unsupported type";
    }
    if ($source === false) return "Error: Could not create image";

    // 6. Kiểm tra dung lượng và quyết định có resize không
    $size = filesize($anhGoc);
    if ($size <= $kichThuocToiDa) {
        // Giữ nguyên kích thước nếu đủ nhỏ
        $thumb = $source;
    } else {
        // Tính toán resize dựa trên dung lượng
        $ratio = sqrt($kichThuocToiDa / $size) * 0.9; // Giảm thêm 10% để chắc chắn
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($source);
    }

    // 7. Tối ưu lưu ảnh - dùng cùng định dạng với ảnh gốc
    $success = false;
    switch ($type) {
        case IMAGETYPE_JPEG: 
            $success = imagejpeg($thumb, $tepDauRa, 85); // Quality 85%
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($thumb, $tepDauRa, 6); // Compression level 6
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($thumb, $tepDauRa);
            break;
    }

    // 8. Dọn dẹp
    imagedestroy($thumb);
    if (isset($tmpFile)) @unlink($tmpFile);

    // Ghi log thời gian xử lý (debug)
    $time = round(microtime(true) - $startTime, 3);
    error_log("Image thumb processed in {$time}s - {$width}x{$height} -> ".($success ? basename($tepDauRa) : 'failed'));

    return $success ? $tepDauRa : "Error: Failed to save";
}
function keySearch($id) {
    $folder = _fetch("SELECT * FROM folders WHERE id='"._sql($id)."'");
    
    if (isset($folder['id'])) {
        if ($folder['_byid'] == 0) {
            $return = $folder['_name'].' '.$folder['id'];
        } else {
            $return = keySearch($folder['_byid']) . " , " . $folder['_name'].' '.$folder['id'];
        }
    } else if($id == 0) {
        return $id;
    } else {
        $return = "";
    }
    
    return $return;
}
function find_array($array, $text) {
    return in_array($text, explode(',', $array));
}


function _thumb($path, $token, $file) { 
    global $root;
    $xroot = "$root/$path/$token.png";
    $database = "$path/$token.png";
    if (strpos($file, 'data:image') !== false) {
        $file = preg_replace('#^data:image/\w+;base64,#i', '', $file);
    }
    $decodedData = base64_decode($file);
    if ($decodedData === false) {
        echo "Error: Base64 decoding failed.";
        return;
    }
    if (!is_dir(dirname($xroot))) {
        mkdir(dirname($xroot), 0755, true);
    }
    if (file_put_contents($xroot, $decodedData) === false) {
        echo "Error: Unable to save the file.";
        return;
    }
    $updateQuery = "UPDATE files SET _thumb='/$database' WHERE _token='$token'";
    if (_query($updateQuery)) {
        echo "Thumbnail successfully created and database updated.";
    } else {
        echo "Error updating the database.";
    }
}

function _size($size) {
    if ($size < 1024) {
        return $size . ' B'; // Byte
    } elseif ($size < 1048576) { // 1024 * 1024
        return round($size / 1024, 2) . ' KB';
    } elseif ($size < 1073741824) { // 1024 * 1024 * 1024
        return round($size / 1048576, 2) . ' MB';
    } elseif ($size < 1099511627776) { // 1024 * 1024 * 1024 * 1024
        return round($size / 1073741824, 2) . ' GB';
    } else {
        return round($size / 1099511627776, 2) . ' TB';
    }
}

function create_dir($path, $permissions = 0755) {
    if (!is_dir($path)) {
        if (!mkdir($path, $permissions, true)) {
            return false;
        }
    }
    return true;
}




