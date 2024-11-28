<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    $userInput = $data['input'] ?? '';

    // 정답과 플래그를 서버 측 상수로 정의
    define('CORRECT_ANSWER', 'aGFja2VyMTIz');
    define('FLAG', 'FLAG{base64_decode_success}');

    try {
        if ($userInput === CORRECT_ANSWER) {
            echo json_encode([
                'success' => true,
                'message' => '정답입니다! 플래그는: ' . FLAG
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