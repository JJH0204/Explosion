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

$userTable = $_SESSION['user_id'];

// FLAG 값 가져오기
$query = "SELECT FLAG FROM $userTable";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'flag' => $row['FLAG']]);
} else {
    echo json_encode(['error' => 'FLAG를 찾을 수 없습니다.']);
}

$conn->close();
?>