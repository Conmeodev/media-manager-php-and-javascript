<?php 
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
 ?>
<div class="container">
    <div class="title" style="background: chocolate;">Danh sách ngẫu nhiên</div>
    <div id="#files" class="block class_rand_post">
        <?php include_once __DIR__."/../../models/view_files.php"; ?>
    </div>
    <span class="btn" id="btnRandPost" onclick="randPost()">Tải thêm ...</span>
</div>