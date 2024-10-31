<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // Sign in 버튼 눌렀을 시 -> 작동X -> 오류 메시지
header('Content-Type: application/json');

// 데이터베이스 연결 설정
$serverIP = '192.168.1.150'; // MySQL 서버 주소
$DB_rootID = "root"; // DB 사용자 이름
$DB_rootPW = "1234"; // DB 비밀번호
$dbname = "testDB"; // 사용할 데이터베이스 이름

// MySQL 연결
$conn = new mysqli($serverIP, $DB_rootID, $DB_rootPW, $dbname);

// 연결 체크
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => '데이터베이스 연결 실패: ' . $conn->connect_error]));
}

$ID = $_POST['ID']; // ID POST로부터 가져옵니다.
$PW = $_POST['PW']; // PW POST로부터 가져옵니다.

// SQL 쿼리 작성 (username과 PW 일치하는지 확인)
$sql = "SELECT * FROM ID_info WHERE ID = ? AND PW = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $ID, $PW); // 사용자 입력을 안전하게 바인딩
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // 인증 성공
    session_start(); // 세션 시작
    $_SESSION['ID'] = $ID; // 세션에 사용자 이름 저장
    $_SESSION['PW'] = $PW; // 세션에 비밀번호 저장

    echo json_encode(['success' => true, 'ID' => $ID]);
} else {
    // 인증 실패
    echo json_encode(['success' => false]);
}

// MySQL 연결 종료
$stmt->close();
$conn->close();
?>
