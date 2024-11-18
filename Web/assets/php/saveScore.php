<?php
session_start();
header('Content-Type: application/json');

// 데이터베이스 연결 설정
$server = "localhost";
$username = "admin";
$password = "flamerootpassword";
$dbname = "testDB";

$conn = new mysqli($server, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// JSON 입력 데이터 가져오기
$input = json_decode(file_get_contents('php://input'), true);

// 디버깅 로그 추가
error_log('Input data: ' . json_encode($input)); // 서버 로그에 기록

$nickname = $input['nickname'] ?? null;
$cardId = $input['cardId'] ?? null;

// 필수 데이터 확인
if (!$nickname || !$cardId) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

// 데이터 삽입
$sql = "INSERT INTO CLEARED_STAGE (NICKNAME, ANSWER) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Database statement preparation failed']);
    exit;
}

$stmt->bind_param("si", $nickname, $cardId);
if ($stmt->execute()) {
    error_log("Saved to database with nickname: $nickname and cardId: $cardId"); // 디버깅 로그 추가
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save to database']);
}

$stmt->close();
$conn->close();
?>
