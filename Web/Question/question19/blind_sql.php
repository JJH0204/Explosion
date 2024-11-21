<?php
header('Content-Type: application/json');

// 데이터베이스 연결 설정
$servername = "localhost";
$username = "root";
$password = "1234";
$dbname = "userDB";

// MySQL 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => '데이터베이스 연결 실패']));
}

// 사용자 입력 받기
$input = $_GET['id'] ?? null;

if ($input) {
    // SQL 쿼리 실행
    $query = "SELECT * FROM users WHERE id = '$input'";
    $result = $conn->query($query);

    // 결과에 따라 참 또는 거짓 반환
    if ($result && $result->num_rows > 0) {
        echo json_encode(['status' => 'true', 'message' => '조건이 참입니다.']);
    } else {
        echo json_encode(['status' => 'false', 'message' => '조건이 거짓입니다.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'id 파라미터가 필요합니다.']);
}

$conn->close();
?>
