<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json'); // 응답을 JSON 형식으로 설정

$serverIP = 'localhost';
$DB_rootID = "admin";
$DB_rootPW = "flamerootpassword";
$dbname = "testDB";

try {
    // 데이터베이스 연결 시도
    $conn = new mysqli($serverIP, $DB_rootID, $DB_rootPW, $dbname);
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }

    // POST 요청으로 전달된 ID, PW, Nickname 받기
    $ID = isset($_POST['ID']) ? $_POST['ID'] : null;
    $PW = isset($_POST['PW']) ? $_POST['PW'] : null;
    $Nickname = isset($_POST['Nickname']) ? $_POST['Nickname'] : null;

    // 필수 입력값 확인
    if ($ID && $PW && $Nickname) {
        // ID 중복 확인
        $checkSql = "SELECT * FROM ID_info WHERE ID = ?";
        $checkStmt = $conn->prepare($checkSql);
        if (!$checkStmt) {
            echo json_encode(['success' => false, 'error' => 'Statement preparation failed: ' . $conn->error]);
            exit;
        }

        $checkStmt->bind_param("s", $ID);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // 중복된 ID가 있는 경우
            echo json_encode(['success' => false, 'error' => 'ID already exists']);
        } else {
            // 새 사용자 정보 삽입
            $sql = "INSERT INTO ID_info (ID, PW, Nickname) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                echo json_encode(['success' => false, 'error' => 'Statement preparation failed: ' . $conn->error]);
                exit;
            }

            $stmt->bind_param("sss", $ID, $PW, $Nickname);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'User registered successfully']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to register user']);
            }

            $stmt->close();
        }

        $checkStmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'ID, PW, or Nickname not set']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
} finally {
    if ($conn) {
        $conn->close();
    }
}
exit;
