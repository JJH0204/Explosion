<?php
session_start();

// 세션에 관리자 인증 확인
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    die("관리자만 접근할 수 있습니다.");
}

// 비밀번호 변경 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_password = $_POST['new_password'];

    // 비밀번호 저장 (단순 파일 저장 예제)
    file_put_contents('admin_password.txt', $new_password);
    echo "비밀번호가 성공적으로 변경되었습니다.";
} else {
    echo "잘못된 요청 방식입니다.";
}
?>
