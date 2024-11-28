<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON 데이터 받기
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    $userFlag = $data['flag'] ?? '';

    // 플래그 검증
    if ($userFlag === 'hidden_flag_123') {
        echo json_encode([
            'success' => true,
            'message' => '축하합니다! 플래그를 찾았습니다',
            'flag' => 'FLAG{source_code_analysis}'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '틀렸습니다. 다시 시도해보세요.'
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