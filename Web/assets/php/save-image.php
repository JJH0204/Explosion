<?php
session_start();
error_reporting(0);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('Invalid request method');
}

if (!isset($_POST['imageUrl']) || !isset($_POST['nickname'])) {
    die('Missing parameters');
}

$imageUrl = $_POST['imageUrl'];
$nickname = $_POST['nickname'];
$uploadDir = __DIR__ . '/../images/custom/';
$flag = "FLag{image_change}";

try {
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            die('Failed to create directory');
        }
    }
    
    $imageContent = @file_get_contents($imageUrl);
    if ($imageContent === false) {
        die('Failed to download image');
    }

    $filename = $uploadDir . $nickname . '.png';
    if (file_put_contents($filename, $imageContent) === false) {
        die('Failed to save image');
    }
    
    chmod($filename, 0644);
    
    echo json_encode([
        'status' => 'success',
        'flag' => $flag
    ]);
    
} catch (Exception $e) {
    die(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
}
?> 