<?php
header('Content-Type: application/json');
include_once __DIR__.'/../_set.php';

set_time_limit(0);
ini_set('memory_limit', '512M');

// Enhanced security check
if(!isset($admin) || !$admin || !isset($user['id']) || !$user['id']) {
    http_response_code(403);
    exit(json_encode(['error' => 'Unauthorized', 'code' => 'AUTH_ERROR']));
}

// Validate required parameters
if(!isset($_POST['fileId']) || !isset($_POST['folders'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Missing required parameters', 'code' => 'INVALID_INPUT']));
}

$fileId = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['fileId']);
$targetFolder = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['folders']);
$storageDir = $root.'/_uploads/_temp/'.$user['id'].'/'.$fileId.'/';

// Check metadata
if(!file_exists($storageDir.'meta.json')) {
    http_response_code(404);
    exit(json_encode(['error' => 'File not found', 'code' => 'FILE_NOT_FOUND']));
}

$meta = json_decode(file_get_contents($storageDir.'meta.json'), true);
if(!$meta || !isset($meta['totalChunks'])) {
    http_response_code(500);
    exit(json_encode(['error' => 'Invalid metadata', 'code' => 'INVALID_META']));
}

// Verify all chunks are present
$chunks = glob($storageDir.'chunk_*.part');
if(count($chunks) !== $meta['totalChunks']) {
    http_response_code(400);
    exit(json_encode([
        'error' => 'Missing chunks',
        'code' => 'INCOMPLETE_FILE',
        'uploaded' => count($chunks),
        'required' => $meta['totalChunks']
    ]));
}

// Prepare target directory
$targetDir = $root.'/_uploads/'.$user['id'].'/'.$targetFolder.'/';
if(!file_exists($targetDir)) {
    if(!mkdir($targetDir, 0777, true)) {
        http_response_code(500);
        exit(json_encode(['error' => 'Could not create target directory', 'code' => 'TARGET_ERROR']));
    }
}

// Generate unique filename
$originalName = $meta['fileName'];
$fileInfo = pathinfo($originalName);
$baseName = $fileInfo['filename'];
$extension = isset($fileInfo['extension']) ? '.'.$fileInfo['extension'] : '';
$counter = 1;

do {
    $finalName = $baseName . ($counter > 1 ? " ($counter)" : "") . $extension;
    $finalPath = $targetDir . $finalName;
    $counter++;
} while(file_exists($finalPath));

// Merge chunks
try {
    $finalFile = fopen($finalPath, 'wb');
    if(!$finalFile) {
        throw new Exception('Could not create final file');
    }
    
    // Sort chunks naturally (chunk_0.part, chunk_1.part, ...)
    natsort($chunks);
    
    // Merge with 2MB buffer
    $bufferSize = 2 * 1024 * 1024;
    foreach($chunks as $chunk) {
        $chunkFile = fopen($chunk, 'rb');
        if(!$chunkFile) {
            throw new Exception("Could not open chunk: $chunk");
        }
        
        while(!feof($chunkFile)) {
            fwrite($finalFile, fread($chunkFile, $bufferSize));
        }
        
        fclose($chunkFile);
        unlink($chunk);
    }
    
    fclose($finalFile);
    
    // Verify final file size
    $finalSize = filesize($finalPath);
    if($finalSize !== $meta['fileSize']) {
        unlink($finalPath);
        throw new Exception("File size mismatch. Expected: {$meta['fileSize']}, Actual: $finalSize");
    }
    
    // Process metadata (assuming processAfterUpload is defined elsewhere)
    $link = "/_uploads/".$user['id']."/$targetFolder/".rawurlencode($finalName);
    if(function_exists('processAfterUpload')) {
        processAfterUpload(
            $finalName,
            $user,
            $targetFolder,
            $link,
            $meta['fileSize'],
            $meta['type'] ?? null,
            $finalPath
        );
    }
    
    // Clean up
    array_map('unlink', glob($storageDir.'*'));
    rmdir($storageDir);
    
    echo json_encode([
        'success' => true,
        'filePath' => $link,
        'fileSize' => $meta['fileSize'],
        'originalName' => $originalName,
        'finalName' => $finalName
    ]);
    
} catch(Exception $e) {
    // Clean up in case of error
    if(isset($finalFile) && is_resource($finalFile)) fclose($finalFile);
    if(isset($finalPath) && file_exists($finalPath)) unlink($finalPath);
    
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'code' => 'MERGE_ERROR',
        'details' => $e->getTraceAsString()
    ]);
}
?>