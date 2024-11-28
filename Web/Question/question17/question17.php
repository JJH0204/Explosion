<?php
header('Content-Type: application/json; charset=UTF-8');

// 플래그 설정
$flag = "FLAG{snake_master}";

// 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 점수 파라미터 확인
    $score = isset($_GET['score']) ? intval($_GET['score']) : 0;
    
    // 점수가 10점 이상인 경우에만 플래그 반환
    if ($score >= 10) {
        echo json_encode([
            'status' => 'success',
            'flag' => $flag
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => '게임 점수가 부족합니다.'
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => '잘못된 요청 방식입니다.'
    ], JSON_UNESCAPED_UNICODE);
}
?>
