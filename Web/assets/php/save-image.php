<?php
/*
    save-image.php
    - Challenge 8 문제 체크 용도
    - 이미지 저장 용도
*/
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 플래그 상수 정의
define('FLAG_VALUE', 'FLag{image_change}');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['imageUrl']) && isset($_POST['nickname'])) {
    $imageUrl = $_POST['imageUrl'];
    $nickname = $_POST['nickname'];
    
    // 디버깅 로그
    error_log("Received request - URL: " . $imageUrl . ", Nickname: " . $nickname);
    
    // 절대 경로 설정
    $save_path = '/var/www/html/assets/images/custom';
    
    // 디렉토리가 없으면 생성
    if (!is_dir($save_path)) {
        if (!mkdir($save_path, 0777, true)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Failed to create directory']);
            exit;
        }
        chmod($save_path, 0777);
    }
    
    // 이미지 다운로드를 위한 컨텍스트 옵션 설정
    $opts = [
        'http' => [
            'method' => 'GET',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3'
            ]
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    $context = stream_context_create($opts);
    
    $extension = strtolower(pathinfo($imageUrl, PATHINFO_EXTENSION));
    if (empty($extension)) {
        $extension = 'jpg';
    }
    
    $filename = $nickname . '.' . $extension;
    $full_path = $save_path . '/' . $filename;
    
    // 이미지 다운로드 시도
    $image_content = @file_get_contents($imageUrl, false, $context);
    if ($image_content === false) {
        error_log("Failed to download image from URL: " . $imageUrl);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Failed to download image']);
        exit;
    }
    
    // 이미지 저장 시도
    if (file_put_contents($full_path, $image_content) !== false) {
        chmod($full_path, 0666);
        error_log("Successfully saved image to: " . $full_path);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'flag' => FLAG_VALUE,
            'message' => 'Image saved successfully'
        ]);
    } else {
        error_log("Failed to save image to: " . $full_path);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Failed to save image']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>