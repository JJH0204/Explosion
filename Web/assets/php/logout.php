<?php
session_start();
$_SESSION = array(); // 세션 변수 초기화

// 세션 쿠키 삭제
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

session_destroy(); // 세션 파괴

// 캐시 방지 헤더 추가
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/json');

echo json_encode(['success' => true]);
exit;