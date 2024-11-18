<?php
header('Content-Type: application/json; charset=UTF-8');

// 플래그 설정
$flag = "FLAG{snake_master}";

// 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode([
        'status' => 'success',
        'flag' => $flag
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => '잘못된 요청 방식입니다.'
    ], JSON_UNESCAPED_UNICODE);
}
?>
