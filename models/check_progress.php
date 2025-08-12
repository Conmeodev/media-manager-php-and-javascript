<?php
/*
Code viết bởi conmeodev
Share vui lòng ghi rõ nguồn để tôn trọng tác giả
link github: https://github.com/Conmeodev
Liên hệ gmail: linkbattu@gmail.com
*/
header('Content-Type: application/json');
include_once __DIR__.'/../_set.php';

if(!isset($_GET['fileId'], $_GET['folders'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Missing parameters']));
}

$fileId = $_GET['fileId'];
$folders = $_GET['folders'];
$storageDir = $root.'/_uploads/_temp/'.$user['id'].'/'.$fileId.'/';

if(!file_exists($storageDir)) {
    exit(json_encode(['uploadedChunks' => []]));
}

$chunks = glob($storageDir.'chunk_*.part');
$uploaded = [];

foreach($chunks as $chunk) {
    if(preg_match('/chunk_(\d+)\.part$/', $chunk, $matches)) {
        $uploaded[] = (int)$matches[1];
    }
}

echo json_encode([
    'uploadedChunks' => $uploaded,
    'totalChunks' => file_exists($storageDir.'meta.json') ? 
        (json_decode(file_get_contents($storageDir.'meta.json'), true)['totalChunks'] ?? 0) : 0
]);
?>