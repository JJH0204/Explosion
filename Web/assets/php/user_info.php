<?php
session_start(); // 세션 시작

// 데이터베이스 연결    
$host = 'localhost'; // 호스트
$db = 'testDB'; // 데이터베이스 이름
$user = 'admin'; // 데이터베이스 사용자
$pass = '1234'; // 데이터베이스 비밀번호

$conn = new mysqli($host, $user, $pass, $db);

// 연결 오류 확인
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// 로그인한 사용자 세션에서 사용자 이름 가져오기
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // 사용자 정보를 JSON 형식으로 반환
    echo json_encode(['username' => $username]);
} else {
    // 로그인하지 않은 경우 에러 메시지 반환
    echo json_encode(['error' => 'User not logged in']);
}

$conn->close(); // 데이터베이스 연결 종료
?>
