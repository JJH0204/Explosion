<?php
/*
    checkSession.php
    - 로그인 체크 후 로그인 상태면 성공 반환
    - 로그인 상태가 아니면 실패 반환 -> index.html 리다이렉트
*/
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'nickname' => $_SESSION['nickname']
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Not logged in'
    ]);
}
?>