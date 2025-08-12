<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
header('Content-Type: application/json');
include_once __DIR__.'/../_set.php';

if(!isset($_POST['fileName'], $_POST['folders'])) {
    echo json_encode(['error' => 'Thiếu tham số']);
    exit;
}

$originalName = basename($_POST['fileName']);
$folders = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['folders']);
$uploadDir = $root.'/_upload/'.$user['id'].'/'.$folders.'/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$counter = 1;
$filename = pathinfo($originalName, PATHINFO_FILENAME);
$extension = pathinfo($originalName, PATHINFO_EXTENSION);
$finalName = $originalName;

while (file_exists($uploadDir . $finalName)) {
    $finalName = "($counter)".$filename.'.'.$extension;
    $counter++;
}

echo json_encode([
    'success' => true,
    'originalName' => $originalName,
    'finalName' => $finalName
]);
?>