<?php
// POST 요청 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON 데이터 받기
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    $userInput = $data['input'] ?? '';

    // 입력값 검증
    if ($userInput === 'admin') {
        echo json_encode([
            'success' => true,
            'flag' => 'ZmxhbWUxYW5zd2Vy'
        ]);
    } else {
        echo json_encode([
            'success' => false
        ]);
    }
} else {
    // POST가 아닌 다른 요청에 대한 처리
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => '잘못된 요청 방식입니다.'
    ]);
}
?> 