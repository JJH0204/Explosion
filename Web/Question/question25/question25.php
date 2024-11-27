<?php
session_start();
header('Content-Type: application/json');

// 로그인 체크
if (!isset($_SESSION['nickname'])) {
    die(json_encode(['error' => '로그인이 필요합니다.']));
}

$host = 'localhost';
$user = $_SESSION['nickname'];
$password = 'firewalld';
$database = 'userDB';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['error' => '데이터베이스 연결 실패: ' . $conn->connect_error]));
}

// 현재 로그인한 사용자의 테이블 이름과 제출된 플래그
$userTable = $_SESSION['user_id'];
$submittedFlag = $_POST['flag'] ?? '';

if (empty($submittedFlag)) {
    die(json_encode(['error' => '플래그가 제출되지 않았습니다.']));
}

// 사용자의 테이블에서 FLAG 값 확인
$query = "SELECT FLAG FROM `$userTable` WHERE FLAG = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $submittedFlag);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    echo json_encode(['success' => true, 'message' => '정답입니다!']);
} else {
    echo json_encode(['success' => false, 'error' => '틀린 답입니다. 다시 시도해주세요.']);
}

$stmt->close();
$conn->close();
?> 