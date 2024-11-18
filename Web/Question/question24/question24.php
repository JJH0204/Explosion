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
$correct_flag = "flag{24Challengesflag}";

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