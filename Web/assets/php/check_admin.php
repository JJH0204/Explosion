<?php
/*
    check_admin.php
    - 관리자 권한 체크
    - 관리자일 시 -> flameadmin.html 리다이렉트
*/
session_start();
header('Content-Type: application/json');

error_log("Checking admin access - Session data: " . print_r($_SESSION, true));

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    error_log("Admin check failed: Not logged in");
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'flame')) {
    error_log("Admin check failed: Unauthorized role - " . ($_SESSION['role'] ?? 'no role'));
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

error_log("Admin check successful for role: " . $_SESSION['role']);
echo json_encode(['success' => true]); 