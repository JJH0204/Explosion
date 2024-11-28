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

// 액션 확인
$action = $_POST['action'] ?? '';

if ($action === 'checkFlag') {
    echo json_encode([
        'success' => true,
        'flag' => 'flag{questionChallenges27flag}'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
}
?> 