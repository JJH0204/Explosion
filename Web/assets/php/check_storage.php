<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$serverIP = 'localhost';
$DB_rootID = "admin";
$DB_rootPW = "flamerootpassword";
$dbname = "testDB";

try {
    $conn = new mysqli($serverIP, $DB_rootID, $DB_rootPW, $dbname);
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed']);
        exit;
    }

    $nickname = isset($_SESSION['nickname']) ? $_SESSION['nickname'] : null;
    $stage = isset($_GET['stage']) ? $_GET['stage'] : null;
    $flag = isset($_GET['flag']) ? $_GET['flag'] : null;

    // LocalStorage 조작 체크
    if ($flag === 'localstorage') {
        $query = "SELECT COUNT(*) as count FROM CLEARED_STAGE WHERE NICKNAME = ? AND ANSWER = 9";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $nickname);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            // DB에 저장된 기록이 없는 경우 (LocalStorage 조작 발견)
            echo json_encode([
                'success' => true,
                'message' => '플래그: FLag{LocalStorage_Modification}'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '이미 완료된 문제입니다.'
            ]);
        }
        $stmt->close();
        $conn->close();
        exit;
    }

    // 기존 스테이지 체크 로직
    if ($nickname && $stage) {
        $query = "SELECT COUNT(*) as count FROM CLEARED_STAGE WHERE NICKNAME = ? AND ANSWER = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $nickname, $stage);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        echo json_encode([
            'success' => true,
            'cleared' => $row['count'] > 0
        ]);
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    }
    
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>