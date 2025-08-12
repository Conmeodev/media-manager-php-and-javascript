<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
function boxFiles($files, $ftype = 'files',$add = "no") {
    $imgDefault = "/asset/image/image-files-thumb-default.png";
    $videoDefault = "/asset/image/video-files-thumb-default.png";
    
    // Xác định loại file
    $fileType = _tf($files['_type']);
    
    // Xác định thumbnail
    $thumb = !empty($files['_thumb']) ? $files['_thumb'] : (($fileType == "image") ? $imgDefault : ($fileType == "video" ? $videoDefault : $imgDefault));
    
    // Thumbnail mặc định cho tải nhanh
    $thumbAjax = ($fileType == "image") ? $imgDefault : (($fileType == "video") ? $videoDefault : $imgDefault);

    return '
    <a href="/' . $ftype . '/' . cvLink($files['_name']). 's/' . $files['id'] . '#fileid' . $files['id'] . '" 
       class="box loadthumb ajax_box ' . $files['_tinhtrang'] . '"
       '.$add.'
       _id="' . $files['id'] . '" 
       _thumb="' . htmlspecialchars($files['_thumb']) . '" 
       _name="' . htmlspecialchars($files['_name']) . '" 
       _uid="' . $files['_uid'] . '" 
       _byid="' . $files['_byid'] . '" 
       _time="' . $files['_time'] . '" 
       _token="' . $files['_token'] . '" 
       _type="' . $files['_type'] . '"  
       _dir="' . $files['_dir'] . '" 
       _size="' . $files['_size'] . '" 
       _box="file" 
       _bbcode="' . $fileType . '">
        <div class="thumb">
            <div class="box-type">' . htmlspecialchars($files['_type']) . '</div>
            <img class="ithumb" _src="' . $thumb . '" src="' . $thumbAjax . '" alt="' . htmlspecialchars(delBBCODE($files['_name'])) . '">
        </div>
        <div class="info-list">
            <div class="list-name">' . bbcode($files['_name']) . '</div>
        </div>
    </a>';
}


function _files($id) {
    $file = _fetch("SELECT * FROM files WHERE id='$id' AND _tinhtrang='danghoatdong'");
    return $file ?: false;
}

function _tf($type) {
    return explode("/", $type, 2)[0] ?? '';
}
