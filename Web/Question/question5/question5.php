<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (isset($input['action']) && $input['action'] === 'getFlag') {
        // 플래그를 서버 측 상수로 정의
        define('ENCRYPTED_FLAG', 'U2FsdGVkX19+MjAyNCtzZWNyZXQra2V5K3RvK2RlY3J5cHQrZmxhZw==');
        
        echo json_encode([
            'success' => true,
            'flag' => ENCRYPTED_FLAG
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '잘못된 요청입니다.'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => '잘못된 요청 방식입니다.'
    ]);
}
?> 