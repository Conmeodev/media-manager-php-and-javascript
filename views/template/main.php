<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if($_SERVER["REQUEST_METHOD"] === "POST") {
    $act = $_POST['act'] ?? null;
    if(if5($act,"addFolders") && $admin) {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $thongbao = "Phiên session hết hạn.";
        } else {
            unset($_SESSION['csrf_token']);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $name = $_POST['name'] ?? null;
            $add = createFolders($user,$name,$getFolders);
            $thongbao = $add;
        }
    }
}
?>
<!--div class="title menu"><?php echo fPath($getFolders);?></div-->
<div class="wrapper">
    <div class="left">
        <div class="container">
            <div class="title"><?php echo fPath($getFolders);?></div>
            <?php echo panelFolders($getFolders); ?>
            <?php
            if ($admin) {
                echo '<div class="menu">';
                if (!empty($thongbao)){
                    echo "<div class='form-alert fail'><ul>$thongbao</ul></div>";
                }
                echo '
                <form action="" method="POST">
                <input type="hidden" name="csrf_token" value="'.$_SESSION['csrf_token'].'">
                <input type="hidden" name="act" value="addFolders">
                <input type="hidden" name="byid" value="'.$getFolders.'">
                <input type="text" name="name" placeholder="Tên thư mục...">
                <button>Thêm</button>
                </form>
                ';
                echo '</div>';
            }
            ?>
            <div id="#folders" class="list">
                <?php
                $count_folders = 0;
                if($admin) {
                    $query_folders = _query("SELECT * FROM folders WHERE  _byid='$getFolders' ORDER BY id DESC");
                } else {
                    $query_folders = _query("SELECT * FROM folders WHERE  _byid='$getFolders' and _tinhtrang='danghoatdong' ORDER BY id DESC");
                    /*if($getFolders == 0){
                        $query_folders = _query("SELECT * FROM folders WHERE _tinhtrang='danghoatdong' ORDER BY id DESC"); 
                    } else {
                        $query_folders = _query("SELECT * FROM folders WHERE  _byid='$getFolders' and _tinhtrang='danghoatdong' ORDER BY id DESC");
                    }*/
                }

                while ($folders = w_fetch($query_folders)) {
                    echo boxFolders($folders);
                    $count_folders++;
                }
                ?>
            </div>
        </div>
    </div>
    <div class="right">
        <div class="container">
            <div class="title">Danh sách tập tin</div>
            <?php
            if($admin) {?>
               <div id="dropzone">
                <span>Kéo thả file hoặc nhấp vào đây để tải lên file</span>
                <input class="none" name="path" value="<?php echo $getFolders; ?>">
                <input class="none" name="act" value="upload">
                <input type="file" id="files" name="files[]" multiple style="display: none;">
            </div>
            <div id="progress-container"></div>
            
        <?php }
        ?>
        <div id="#files" _ad class="block">
            <?php
            $count_files = 0;
            if($admin) {
                $query_files = _query("SELECT * FROM files WHERE  _byid='$getFolders' ORDER BY id DESC");
            } else {
                $query_files = _query("SELECT * FROM files WHERE  _byid='$getFolders' and _tinhtrang='danghoatdong' ORDER BY id DESC");
            }
            $bbcode_all = null;
                $output = ""; // Khởi tạo trước vòng lặp
                while ($files = w_fetch($query_files)) {
                    echo boxFiles($files);
                    $count_files++;
                    $type = _tf($files['_type']);
                    $fileContent = "";

                    if ($type == "video") {
                        $fileContent .= '[video]https://' . $domain1 . $files['_dir'] . '[/video] https://' . $domain1 . "/files/bbcode/" . $files['id'] . "\n";
                    } elseif ($type == "image") {
                        $fileContent .= '[img]https://' . $domain1 . $files['_dir'] . '[/img] https://' . $domain1 . "/files/bbcode/" . $files['id'] . "\n";
                    }

    // Nếu tổng nội dung vượt 10000 ký tự thì xuất ra ngay, sau đó reset
                    if (strlen($output) + strlen($fileContent) > 10000) {
                        echo "<textarea>" . htmlspecialchars($output) . "</textarea>";
        $output = ""; // Reset để tiếp tục gom
    }

    $output .= $fileContent;
}

// Sau vòng lặp, nếu còn dữ liệu thì in ra
if (strlen($output) > 0) {
    echo "<textarea style='height:unset'>" . htmlspecialchars($output) . "</textarea>";
}

if($count_files == 0) {
    $notfi =  '<div class="menu">Không có tập tin nào tại đây.</div>';
}
?>
</div>
<?php 
            //include_once __DIR__."/post_random.php";
?>
</div>
</div>
</div>