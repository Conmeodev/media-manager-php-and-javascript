<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
include_once __DIR__."../../_set.php";
        $query_files = _query("SELECT * FROM files WHERE _tinhtrang = 'danghoatdong' ORDER BY RAND() LIMIT 10;");
        while ($files = w_fetch($query_files)) {
            echo boxFiles($files,"files","ajax");
        }
        ?>
