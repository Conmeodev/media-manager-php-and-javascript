<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com

*/
include_once '_db_config.php';
$mailEmail = ""; //ví dụ: linkbattu@gmail.com
$mailPass = ""; //mật khẩu SMTP

$ketnoi = mysqli_connect(db_host,db_user,db_pass,db_name);
if($ketnoi) {
	mysqli_set_charset($ketnoi,'utf8mb4');
} else {
	//header("location:/install");
	echo "Ket noi csdl";
}

function _query($txt){
    GLOBAL $ketnoi;
    return mysqli_query($ketnoi,$txt);
}
function _fetch($txt) {
    return mysqli_fetch_array(_query($txt));
}
function w_fetch($txt) {
    return mysqli_fetch_array($txt);
}
function _fetch_all($txt) {
    $result = _query($txt);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function _sql($txt){
    GLOBAL $ketnoi;
    return mysqli_real_escape_string($ketnoi,$txt);
}
function _insert($table, $columns, $values) {
    GLOBAL $ketnoi;
    if (count($columns) !== count($values)) {
        die("Lỗi: Số lượng cột và giá trị không khớp nhau.");
    }
    $columns_str = implode(", ", $columns);
    $placeholders = implode(", ", array_fill(0, count($values), "?"));
    $sql = "INSERT INTO `$table` ($columns_str) VALUES ($placeholders)";

    // Chuẩn bị truy vấn
    $stmt = mysqli_prepare($ketnoi, $sql);
    if (!$stmt) {
        die("Lỗi chuẩn bị truy vấn: " . mysqli_error($ketnoi));
    }
    $types = str_repeat("s", count($values)); 
    mysqli_stmt_bind_param($stmt, $types, ...$values);
    $result = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}
function _update($table, $data, $where) {
    global $ketnoi;
    $set = [];
    foreach ($data as $key => $value) {
        $set[] = "`$key` = '" . mysqli_real_escape_string($ketnoi, $value) . "'";
    }
    $set = implode(", ", $set);
    $where_clause = [];
    foreach ($where as $key => $value) {
        $where_clause[] = "`$key` = '" . mysqli_real_escape_string($ketnoi, $value) . "'";
    }
    $where_clause = implode(" AND ", $where_clause);
    $sql = "UPDATE `$table` SET $set WHERE $where_clause";
    return _query($sql);
}
