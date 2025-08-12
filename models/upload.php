<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
include_once $_SERVER['DOCUMENT_ROOT']."/_set.php";

// Kiểm tra quyền admin
if(!$admin) {
    header("location:$u_404");
    exit;
}

// Kiểm tra và tạo thư mục upload
if (!is_dir($root."/_uploads")) {
    mkdir($root."/_uploads", 0755, true);
}

// Xử lý thư mục đích
$folders = isset($_POST['folders']) ? $_POST['folders'] : "0";
if ($folders != 0) {
    $sFolders = _fetch("SELECT * FROM folders WHERE id='$folders' and _uid='".$user['id']."'");
    $issetPath = isset($sFolders['id']) ? "ok" : "no";
} else {
    $issetPath = "ok";
}

// Kiểm tra người dùng và quyền
if (!$user) {
    die(json_encode(['error' => 'Bạn phải đăng nhập để sử dụng chức năng này.']));
} elseif ($issetPath != "ok") {
    die(json_encode(['error' => 'Thư mục cha không thuộc quyền của bạn.']));
}

// Kiểm tra file upload
if (!isset($_FILES['file'])) {
    die(json_encode(['error' => 'Không có file được gửi lên.']));
}

// Lấy thông tin từ POST
$fileName = $_POST['fileName'];
$_type = $_POST['type'];
$chunkNumber = intval($_POST['chunkNumber']);
$totalChunks = intval($_POST['totalChunks']);
$size = intval($_POST['fileSize']);
$link = "/_uploads/".$user['id']."/$folders/$fileName";
$uploadDir = $root."/_uploads/".$user['id']."/$folders";
$finalFilePath = "$uploadDir/$fileName";

// Tạo thư mục nếu chưa tồn tại
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Xử lý upload file (cả chunk và file nhỏ)
if ($totalChunks > 1) {
    // Xử lý upload từng chunk
    $chunkDir = "$uploadDir/{$fileName}_chunks/";
    
    if (!is_dir($chunkDir)) {
        mkdir($chunkDir, 0755, true);
    }
    
    $chunkFile = $chunkDir . 'chunk_' . $chunkNumber;
    
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $chunkFile)) {
        die(json_encode(['error' => 'Không thể di chuyển chunk được tải lên.']));
    }
    
    // Kiểm tra nếu đã upload đủ chunks
    $chunks = glob($chunkDir . 'chunk_*');
    if (count($chunks) == $totalChunks) {
        // Ghép các chunk lại
        $fp = fopen($finalFilePath, 'wb');
        if (!$fp) {
            die(json_encode(['error' => 'Không thể tạo tệp cuối cùng.']));
        }
        
        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFile = $chunkDir . 'chunk_' . $i;
            if (file_exists($chunkFile)) {
                $chunkData = file_get_contents($chunkFile);
                fwrite($fp, $chunkData);
                unlink($chunkFile);
            } else {
                die(json_encode(['error' => 'Thiếu chunk: ' . $i]));
            }
        }
        fclose($fp);
        rmdir($chunkDir);
        
        // Xử lý sau khi upload hoàn tất
        processAfterUpload($fileName, $user, $folders, $link, $size, $_type, $finalFilePath);
        die(json_encode(['success' => 'Tải lên tệp thành công.']));
    } else {
        die(json_encode(['success' => 'Chunk ' . $chunkNumber . ' đã tải lên thành công.']));
    }
} else {
    // Xử lý file nhỏ (không chia chunk)
    if (!move_uploaded_file($_FILES['file']['tmp_name'], $finalFilePath)) {
        die(json_encode(['error' => 'Không thể di chuyển file được tải lên.']));
    }
    
    // Xử lý sau khi upload hoàn tất
    processAfterUpload($fileName, $user, $folders, $link, $size, $_type, $finalFilePath);
    die(json_encode(['success' => 'Tải lên tệp thành công.']));
}

// Hàm xử lý sau khi upload hoàn tất (giữ nguyên quy trình xử lý ảnh)
function processAfterUpload($fileName, $user, $folders, $link, $size, $_type, $finalFilePath) {
    global $root, $upload_thumb_vps, $ip_vps, $domain, $time;
    
    $token = md5($fileName . time() . $size);
    $thumb_ins = "";
    
    // Xử lý thumbnail cho video
    if ($upload_thumb_vps == "on" && _tf($_type) == "video") {
        $thumb_folders = "_thumbs/".$user['id']."/".$folders;
        $link_vps_thumb = file_get_contents("http://$ip_vps/thumbVideo.php?act=CREATE_THUMB&linkVideo=http://$domain$link");
        
        if (!is_dir($root."/".$thumb_folders)) {
            mkdir($root."/".$thumb_folders, 0755, true);
        }
        
        if (!empty($link_vps_thumb)) {
            _imageThumb("102400", $link_vps_thumb, "$root/$thumb_folders/$token.png");
            
            if (file_exists("$root/$thumb_folders/$token.png")) {
                $thumb_ins = "/$thumb_folders/$token.png";
            }
            
            // Xóa thumbnail tạm trên VPS
            file_get_contents("http://$ip_vps/thumbVideo.php?act=DELETE_THUMB&linkThumb=$link_vps_thumb");
        }
    }
    
    // Xử lý thumbnail cho ảnh (giữ nguyên như code cũ)
    if (_tf($_type) == "image") {
        $thumb_folders = "_thumbs/".$user['id']."/".$folders;
        $thumb_name = $token.".png";
        
        if (!is_dir($root."/".$thumb_folders)) {
            mkdir($root."/".$thumb_folders, 0777, true);
        }
        
        $create = _imageThumb("10240", $finalFilePath, $root."/".$thumb_folders."/".$thumb_name);
        if ($create == "not") {
            $thumb_ins = _sql($link);
        } else {
            $thumb_ins = "/$thumb_folders/$thumb_name";
        }
    }
    
    // Lưu thông tin vào database
    $keysearch = keySearch($folders) . ' ' . _sql($fileName);
    _query("INSERT INTO files(_name, _uid, _byid, _dir, _time, _token, _size, _tinhtrang, _type, _thumb, keysearch) 
            VALUES('" . _sql($fileName) . "', '" . _sql($user['id']) . "', '" . _sql($folders) . "', 
            '" . _sql($link) . "', '$time', '$token', '$size', 'danghoatdong', '$_type', '$thumb_ins', '$keysearch')");
}
?>