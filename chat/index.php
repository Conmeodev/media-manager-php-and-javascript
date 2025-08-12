<?php
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);
include "../_set.php";
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
// Kiểm tra đăng nhập - bắt buộc phải đăng nhập mới được chat

if (!isset($user)) {
    /*header("Location: /views/login.php#login");
    exit;
    */
    $msg_error = "Cần <a href='/views/login.php#login'>đăng nhập</a> để gửi tin nhắn";
}


// Cấu hình admin
$admin_level = 10; // Cấp độ admin



// Chống spam: 1 tin / 5 giây
if (isset($_POST['message']) && isset($user['id'])) {
    if (!isset($_SESSION['last_send_time']) || time() - $_SESSION['last_send_time'] >= 5) {
        $content = trim($_POST['message']);
        
        // Kiểm tra độ dài tin nhắn
        $max_length = ($user['_capdo'] == $admin_level) ? 5000 : 1000;
        
        if (strlen($content) > $max_length) {
            $msg_error = "Nội dung quá dài (tối đa {$max_length} ký tự).";
        } elseif ($content != "") {
            $_SESSION['last_send_time'] = time();
            $uid = _sql($user['id']);
            $content = _sql(strip_tags($content));
            $ip = _sql($_SERVER['REMOTE_ADDR']);
            
            _insert("messages", ["username", "content", "ip"], [$uid, $content, $ip]);
            header("Location: ".$_SERVER['PHP_SELF']);
            exit;
        }
    } else {
        $msg_error = "Bạn đang gửi quá nhanh. Vui lòng đợi 5 giây.";
    }
}

// Hàm xử lý BBCode (giữ nguyên từ code gốc)
function _bbcode($text) {
    $text = htmlspecialchars($text); // Ngăn XSS

    // Ưu tiên video
    preg_match_all('/\[video\](.*?)\[\/video\]/is', $text, $video_matches);
    $video_html = '';
    foreach ($video_matches[1] as $video_url) {
        $video_url = htmlspecialchars($video_url);
        $video_html .= '<video controls style="max-width:100%;height:auto;"><source src="'.$video_url.'"></video><br>';
    }

    // Xử lý ảnh theo dạng lưới
    preg_match_all('/\[img\](.*?)\[\/img\]/is', $text, $img_matches);
    $img_html = '';
    if (!empty($img_matches[1])) {
        $img_html = '<div class="image-grid">';
        foreach ($img_matches[1] as $img_url) {
            $img_url = htmlspecialchars($img_url);
            $img_html .= '<div class="image-item"><img src="'.$img_url.'" alt="Image" style="max-width:100%; height:auto;"></div>';
        }
        $img_html .= '</div>';
    }

    // Sau đó xóa các [video] và [img] trong text để tránh lặp
    $text = preg_replace('/\[video\].*?\[\/video\]/is', '', $text);
    $text = preg_replace('/\[img\].*?\[\/img\]/is', '', $text);

    // Các BBCode cơ bản còn lại
    $find = [
        '/\[b\](.*?)\[\/b\]/is',
        '/\[i\](.*?)\[\/i\]/is',
        '/\[u\](.*?)\[\/u\]/is',
        '/\[url\](.*?)\[\/url\]/is',
        '/\[url=(.*?)\](.*?)\[\/url\]/is',
        '/\[color=(.*?)\](.*?)\[\/color\]/is',
    ];
    $replace = [
        '<strong>$1</strong>',
        '<em>$1</em>',
        '<u>$1</u>',
        '<a href="$1" target="_blank">$1</a>',
        '<a href="$1" target="_blank">$2</a>',
        '<span style="color:$1">$2</span>',
    ];

    $text = preg_replace($find, $replace, $text);

    return $video_html . $img_html . nl2br($text);
}

// Cập nhật phân trang
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1; // Lấy số trang
$limit = 20; // Số tin nhắn mỗi trang
$offset = ($page - 1) * $limit; // Vị trí bắt đầu trong cơ sở dữ liệu

// Lấy tổng số tin nhắn
$total = _fetch("SELECT COUNT(*) as total FROM messages")['total'];
$total_pages = ceil($total / $limit); // Tính tổng số trang

// Lấy tin nhắn của trang hiện tại
$messages = _fetch_all("SELECT * FROM messages ORDER BY id DESC LIMIT $offset, $limit");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Phòng Chat <?php echo $domain;?></title>
    <style>
    body {
        font-family: "Segoe UI", Roboto, sans-serif;
        background: linear-gradient(135deg, #e0eafc, #cfdef3);
        margin: 0; padding: 0;
    }
    .container {
        max-width: 700px;
        margin: 30px auto;
        background: rgba(255,255,255,0.8);
        backdrop-filter: blur(8px);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }
    form {
        text-align: center;
        margin-bottom: 20px;
    }
    input[type="text"],
    input[type="password"],
    textarea {
        width: 90%;
        max-width: 500px;
        padding: 12px 15px;
        margin: 8px 0;
        border: 1px solid #ccc;
        border-radius: 12px;
        background: #f9f9f9;
        font-size: 16px;
        transition: border 0.2s;
    }
    input[type="text"]:focus,
    input[type="password"]:focus,
    textarea:focus {
        border: 1px solid #0078d7;
        outline: none;
    }
    textarea {
        height: 100px;
        resize: vertical;
    }
    input[type="submit"] {
        padding: 10px 20px;
        border: none;
        background: #0078d7;
        color: white;
        border-radius: 12px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s;
    }
    input[type="submit"]:hover {
        background: #005fa3;
    }
    .chat-box {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 20px;
    }
    .msg {
        max-width: 80%;
        padding: 12px 16px;
        border-radius: 18px;
        line-height: 1.5;
        font-size: 15px;
        position: relative;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        backdrop-filter: blur(3px);
    }
    .me {
        align-self: flex-end;
        background: #d1eaff;
        color: #003865;
        border-top-right-radius: 0;
    }
    .you {
        align-self: flex-start;
        background: #ffffff;
        color: #222;
        border-top-left-radius: 0;
    }
    .username {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 3px;
        color: #444;
    }
    .time {
        font-size: 11px;
        color: #777;
        margin-top: 5px;
        text-align: right;
    }
    .pagination {
        text-align: center;
        margin: 20px 0;
    }
    .pagination a {
        display: inline-block;
        padding: 6px 12px;
        margin: 2px;
        background: #0078d7;
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-size: 14px;
        transition: background 0.2s;
    }
    .pagination a:hover {
        background: #005fa3;
    }
    .error {
        color: #b20000;
        background: #ffe5e5;
        padding: 10px;
        border-radius: 10px;
        max-width: 500px;
        margin: 10px auto;
    }
    .admin-badge {
        color: red;
        font-size: 12px;
        margin-left: 5px;
    }
    .user-badge {
        color: green;
        font-size: 12px;
        margin-left: 5px;
    }
    @media screen and (max-width: 600px) {
        .msg { max-width: 100%; font-size: 14px; }
        .container { border-radius: 0; margin: 0; }
    }
    
    .image-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        padding: 10px 0;
    }

    .image-item {
        display: flex;
        justify-content: center;
        align-items: center;
        border: 1px solid #ddd;
        padding: 5px;
        border-radius: 5px;
    }

    .image-item img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
    }
    </style>
</head>
<body>
<div class="container">
    <h2>Phòng Chat</h2>
    <center><a href="/">Về trang chủ</a> | <a href="/chat">Làm mới</a></center>
    
    <p style="text-align:center;">
        Xin chào <strong><?= htmlspecialchars($user['_taikhoan']) ?></strong>
        <?php if($user['_capdo'] == $admin_level): ?>
            <span class="admin-badge">✪ Quản trị viên</span>
        <?php else: ?>
            <span class="user-badge">❖ Thành viên</span>
        <?php endif; ?>
    </p>
    
    <form method="post">
        <textarea name="message" placeholder="Nhập tin nhắn..." required></textarea><br>
        <input type="submit" value="Gửi">
        <?php if (isset($msg_error)) echo "<div class='error'>$msg_error</div>"; ?>
    </form>

    <div class="chat-box">
        <?php foreach ($messages as $msg): 
            $is_me = $user['id'] === $msg['username'];
            $msg_class = $is_me ? 'me' : 'you';
            $gUser = _fetch("SELECT * FROM user WHERE id = '"._sql($msg['username'])."'");
            
        ?>
            <div class="msg <?= $msg_class ?>">
                <div class="username">
                    <?= htmlspecialchars($gUser['_taikhoan']) ?> 
                    <?php if($gUser['_capdo'] == "10"): ?>
                        <span class="admin-badge">✪ Quản trị viên</span>
                    <?php else: ?>
                        <span class="user-badge">❖ Thành viên</span>
                    <?php endif; ?>
                </div>
                <?= _bbcode($msg['content']) ?>
                <div class="time"><?= $msg['created_at'] ?> <?php /*$msg['ip']*/ ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <?php for ($i=1; $i<=$total_pages; $i++): ?>
            <a href="?page=<?= $i ?>"><?= $i == $page ? "<strong>$i</strong>" : $i ?></a>
        <?php endfor; ?>
    </div>
</div>
</body>
</html>