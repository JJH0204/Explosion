<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['nickname']) || !isset($_POST['flag'])) {
    die(json_encode(['error' => '잘못된 접근입니다.']));
}

$host = 'localhost';
$user = 'admin';
$password = 'flamerootpassword';
$database = 'userDB';

// admin 계정으로 데이터베이스 연결
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['error' => '데이터베이스 연결 실패: ' . $conn->connect_error]));
}

// 사용자의 닉네임과 입력한 플래그 가져오기
$nickname = $conn->real_escape_string($_SESSION['nickname']);
$inputFlag = $conn->real_escape_string($_POST['flag']);

// 해당 사용자의 테이블에서 플래그 확인
$query = "SELECT flag FROM `$nickname` WHERE flag = '$inputFlag'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => '잘못된 플래그입니다.']);
}

$conn->close();
?> 