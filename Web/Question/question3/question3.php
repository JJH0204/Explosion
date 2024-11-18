<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    $userInput = $data['input'] ?? '';

    // Base64로 디코딩된 값이 'hacker123'인지 확인
    try {
        if (base64_decode($userInput) === 'hacker123') {
            echo json_encode([
                'success' => true,
                'flag' => 'FLAG{base64_decode_success}'
            ]);
        } else {
            echo json_encode([
                'success' => false
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => '에러 발생'
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