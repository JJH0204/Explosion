<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 디버깅을 위한 에러 로깅 활성화
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // 필수 파라미터 확인
    if (!isset($_POST['imageUrl']) || !isset($_POST['nickname'])) {
        echo 'error: missing parameters';
        exit;
    }

    $imageUrl = filter_var($_POST['imageUrl'], FILTER_SANITIZE_URL);
    $nickname = filter_var($_POST['nickname'], FILTER_SANITIZE_STRING);
    
    // 현재 스크립트 디렉토리를 기준으로 상대 경로 설정
    $uploadDir = dirname(__FILE__) . '/../images/custom/';
    
    try {
        // 디렉토리가 없으면 생성 시도
        if (!is_dir($uploadDir)) {
            if (!@mkdir($uploadDir, 0777, true)) {
                $error = error_get_last();
                error_log("Directory creation failed: " . $error['message']);
                echo "error: failed to create directory - " . $error['message'];
                exit;
            }
        }
        
        // 이미지 다운로드
        $imageContent = @file_get_contents($imageUrl);
        if ($imageContent === false) {
            echo 'error: failed to download image';
            exit;
        }

        // 파일 저장
        $filename = $uploadDir . $nickname . '.png';
        if (file_put_contents($filename, $imageContent)) {
            chmod($filename, 0644);
            echo 'success';
        } else {
            echo 'error: failed to save image';
        }
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo 'error: ' . $e->getMessage();
    }
} else {
    echo 'error: invalid request method';
}
?>