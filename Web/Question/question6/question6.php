<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

try {
    // POST 데이터 받기
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if (!$data || !isset($data['score'])) {
        throw new Exception('잘못된 입력입니다.');
    }

    $score = intval($data['score']);
    
    // 점수 유효성 검사
    if ($score < 0 || $score > 10000) {
        throw new Exception('유효하지 않은 점수입니다.');
    }

    // 여기에 점수 저장 로직을 추가할 수 있습니다
    // 예: 데이터베이스에 저장

    $response = [
        'success' => true,
        'message' => '점수가 저장되었습니다.',
        'score' => $score
    ];

    // 100ms 미만일 경우 flag 추가
    if ($score < 100) {
        $response['flag'] = "FLAG{F4st_R3fl3x_M4st3r}";
    }

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>