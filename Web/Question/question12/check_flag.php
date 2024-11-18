<?php
header('Content-Type: application/json');

// CSRF 방지
session_start();
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    die(json_encode(['success' => false, 'message' => 'Invalid request']));
}

// POST 요청 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid method']));
}

// 제출된 플래그 가져오기
$submitted_flag = $_POST['flag'] ?? '';

// Base64로 인코딩된 원본 메시지
$encoded_message = "U1RBVEVfT0ZfQ09ERTpKcG5sMTIzJEtlY2hfU2VjcmV0";

// 디코딩 및 플래그 추출
$decoded_message = base64_decode($encoded_message);
$correct_flag = explode(':', $decoded_message)[1];

// 플래그 검증
if ($submitted_flag === $correct_flag) {
    echo json_encode([
        'success' => true,
        'flag' => $correct_flag
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid flag'
    ]);
}
?> 