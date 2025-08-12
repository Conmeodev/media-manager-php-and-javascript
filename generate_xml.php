<?php
require_once "_config.php"; // Load file cấu hình
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
// Tạo buffer đầu ra
ob_start();

// Bắt đầu file XML
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

// Hàm tạo URL hợp lệ
function createSitemapUrl($loc, $lastmod = null, $changefreq = "weekly", $priority = "0.8") {
    $url = "    <url>\n";
    $url .= "        <loc>" . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . "</loc>\n";
    if ($lastmod) {
        $url .= "        <lastmod>" . date("Y-m-d", $lastmod) . "</lastmod>\n";
    }
    $url .= "        <changefreq>$changefreq</changefreq>\n";
    $url .= "        <priority>$priority</priority>\n";
    $url .= "    </url>\n";
    return $url;
}

// **1️⃣ Lấy danh sách files**
$query_files = "SELECT * FROM `files`";
$result_files = _query($query_files);
$domain = ""; // Đổi thành domain của bạn

if ($result_files) {
    while ($row = mysqli_fetch_assoc($result_files)) {
        $url = "$domain/files/{$row['_token']}/{$row['id']}";
        echo createSitemapUrl($url, $row['_time']);
    }
}

// **2️⃣ Lấy danh sách folders**
$query_folders = "SELECT * FROM `folders`";
$result_folders = _query($query_folders);

if ($result_folders) {
    while ($row = mysqli_fetch_assoc($result_folders)) {
        $url = "$domain/folders/{$row['_token']}/{$row['id']}";
        echo createSitemapUrl($url, $row['_time']);
    }
}

echo "</urlset>\n";

// **Lưu file sitemap.xml**
$file = "sitemap.xml";
$data = ob_get_clean();

if (file_put_contents($file, $data) !== false) {
    echo "✅ Đã tạo thành công <a href='$file' target='_blank'>sitemap.xml</a>";
} else {
    echo "❌ Lỗi: Không thể ghi file sitemap.xml. Hãy kiểm tra quyền ghi.";
}
?>
