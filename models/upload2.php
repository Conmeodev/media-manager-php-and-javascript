<?php
header('Content-Type: application/json');
include_once __DIR__.'/../_set.php';

// Increase limits
set_time_limit(0);
ini_set('memory_limit', '512M');

// Enhanced security check
if(!isset($admin) || !$admin || !isset($user['id']) || !$user['id']) {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized', 'code' => 'AUTH_ERROR']));
}

// Validate all required inputs
$required = ['fileId', 'chunkIndex', 'totalChunks', 'fileName', 'folders', 'fileSize', 'chunkHash'];
foreach($required as $field) {
    if(!isset($_POST[$field])) {
        http_response_code(400);
        exit(json_encode(['error' => "Missing required field: $field", 'code' => 'INVALID_INPUT']));
    }
}

// Sanitize inputs
$fileId = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['fileId']);
$chunkIndex = (int)$_POST['chunkIndex'];
$totalChunks = (int)$_POST['totalChunks'];
$fileName = basename($_POST['fileName']);
$folders = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['folders']);
$chunkHash = $_POST['chunkHash'];

// Validate chunk index
if($chunkIndex < 0 || $chunkIndex >= $totalChunks) {
    http_response_code(400);
    exit(json_encode(['error' => 'Invalid chunk index', 'code' => 'INVALID_CHUNK']));
}

// Prepare storage directory
$storageDir = $root.'/_uploads/_temp/'.$user['id'].'/'.$fileId.'/';
if(!file_exists($storageDir)) {
    if(!mkdir($storageDir, 0777, true)) {
        http_response_code(500);
        exit(json_encode(['error' => 'Could not create storage directory', 'code' => 'STORAGE_ERROR']));
    }
    
    // Create metadata file
    $meta = [
        'fileName' => $fileName,
        'fileSize' => (int)$_POST['fileSize'],
        'totalChunks' => $totalChunks,
        'createdAt' => time(),
        'userId' => $user['id']
    ];
    
    if(!file_put_contents($storageDir.'meta.json', json_encode($meta))) {
        http_response_code(500);
        exit(json_encode(['error' => 'Could not create metadata file', 'code' => 'META_ERROR']));
    }
}

// Handle chunk upload
try {
    $chunkPath = $storageDir.'chunk_'.$chunkIndex.'.part';
    
    // Check if chunk already exists and is valid
    if(file_exists($chunkPath)) {
        $existingHash = md5_file($chunkPath);
        if($existingHash === $chunkHash) {
            echo json_encode(['success' => true, 'chunkIndex' => $chunkIndex, 'status' => 'already_uploaded']);
            exit;
        }
        // Remove invalid chunk
        unlink($chunkPath);
    }
    
    // Save chunk with stream to reduce memory usage
    if(!isset($_FILES['chunk']['tmp_name'])) {
        http_response_code(400);
        exit(json_encode(['error' => 'No chunk data received', 'code' => 'NO_CHUNK_DATA']));
    }
    
    $tempPath = $_FILES['chunk']['tmp_name'];
    
    // Verify uploaded chunk
    $uploadedHash = md5_file($tempPath);
    if($uploadedHash !== $chunkHash) {
        unlink($tempPath);
        throw new Exception('Chunk verification failed. Hashes do not match.');
    }
    
    // Move to permanent location
    if(!move_uploaded_file($tempPath, $chunkPath)) {
        throw new Exception('Could not save chunk file');
    }
    
    // Check completion status
    $uploadedChunks = glob($storageDir.'chunk_*.part');
    $uploadedCount = count($uploadedChunks);
    
    $response = [
        'success' => true,
        'chunkIndex' => $chunkIndex,
        'uploaded' => $uploadedCount,
        'remaining' => $totalChunks - $uploadedCount
    ];
    
    if($uploadedCount === $totalChunks) {
        $response['action'] = 'merge';
        $response['fileId'] = $fileId;
    }
    
    echo json_encode($response);
    
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'code' => 'CHUNK_UPLOAD_ERROR',
        'details' => $e->getTraceAsString()
    ]);
}
?>