<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
$keysearch = isset($_POST['keysearch']) ? trim($_POST['keysearch']) : '';
$searchResults = [
    'folders' => [],
    'files' => []
];

if (!empty($keysearch)) {
    $_sqlKey = _sql($keysearch);
    $c = 0;

    // Tìm kiếm trong folders
    $searchFolders = _query("SELECT * FROM folders WHERE keysearch LIKE '%$_sqlKey%' AND _tinhtrang='danghoatdong'");
    while ($folder = w_fetch($searchFolders)) {
        $c++;
        $folder["_thumb"] = getThumbFolders($folder['_thumb'], $folder['id']);
        $searchResults['folders'][] = $folder;
    }

    // Tìm kiếm trong files
    $searchFiles = _query("SELECT * FROM files WHERE keysearch LIKE '%$_sqlKey%' AND _tinhtrang='danghoatdong'");
    while ($file = w_fetch($searchFiles)) {
        $c++;
        $searchResults['files'][] = $file;
    }

    // Lưu lịch sử tìm kiếm
    if (!empty($keysearch) && isset($ip, $time)) {
        if (isset($user) && $user["_capdo"] < 10) {
            _query("INSERT INTO `search` (`_key`, `_ip`, `_time`, `_count`) VALUES ('$_sqlKey','$ip','$time','$c')");
        } elseif (!isset($user)) {
            _query("INSERT INTO `search` (`_key`, `_ip`, `_time`, `_count`) VALUES ('$_sqlKey','$ip','$time','$c')");
        }
    }
}

// Lịch sử tìm kiếm
if (isset($user) && $user["_capdo"] == 10 && isset($_GET['his'])) {
    $items_per_page = 10;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($current_page < 1) $current_page = 1;

    $offset = ($current_page - 1) * $items_per_page;
    $total_items_result = _query("SELECT COUNT(*) as total FROM `search`");
    $total_items_row = w_fetch($total_items_result);
    $total_items = $total_items_row['total'] ?? 0;

    $data_result = _query("SELECT * FROM `search` ORDER BY `_time` DESC LIMIT $offset, $items_per_page");
}
?>

<div class="wrapper flip">
    <div class="right">
        <?php if (!empty($keysearch)): ?>
        <div class="menu" id="kq">
            <h2><?php echo $c; ?> Kết quả tìm kiếm cho: "<?php echo htmlspecialchars($keysearch); ?>"</h2>
            
            <?php if (!empty($searchResults['folders']) || !empty($searchResults['files'])): ?>
                <!-- Hiển thị thư mục -->
                <?php if (!empty($searchResults['folders'])): ?>
                    <h3>Thư mục</h3>
                    <div id="folders" class="block">
                        <?php foreach ($searchResults['folders'] as $folder): ?>
                            <?php echo boxFiles($folder,'folders'); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Hiển thị tệp -->
                <?php if (!empty($searchResults['files'])): ?>
                    <h3>Tệp tin</h3>
                    <div id="files" class="block">
                        <?php foreach ($searchResults['files'] as $file): ?>
                            <?php echo boxFiles($file); ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <p>Không tìm thấy kết quả nào.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="left">
    <div class="container"></div>
</div>
</div>
