<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json'); // 응답을 JSON 형식으로 설정

$serverIP = 'localhost';
$DB_rootID = "admin";
$DB_rootPW = "1234";
$dbname = "testDB";
$charset = 'utf8mb4';
 
try {
    // 데이터베이스 연결 시도
    $conn = new mysqli($serverIP, $DB_rootID, $DB_rootPW, $dbname);
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }

    $ID = isset($_POST['ID']) ? $_POST['ID'] : null;
    $PW = isset($_POST['PW']) ? $_POST['PW'] : null;

    if ($ID && $PW) {
        $sql = "SELECT * FROM ID_info WHERE ID = ? AND PW = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => 'Statement preparation failed: ' . $conn->error]);
            exit;
        }
        
        $stmt->bind_param("ss", $ID, $PW);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            session_start();
            $_SESSION['user_id'] = $user['ID'];  // user_id 저장
            $_SESSION['username'] = $user['NICKNAME'];  // username 저장

            echo json_encode(['success' => true, 'ID' => $user['ID'], 'NICKNAME' => $user['NICKNAME']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid ID or password']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'ID or PW not set']);
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
