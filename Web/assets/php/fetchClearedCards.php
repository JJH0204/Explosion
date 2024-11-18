<?php
session_start();

// 로그인 확인
if (!isset($_SESSION['nickname'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

$nickname = $_SESSION['nickname'];

try {
    // 데이터베이스 연결
    $conn = new mysqli('localhost', 'admin', 'flamerootpassword', 'testDB');
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }

    // CLEARED_STAGE 테이블에서 클리어한 ANSWER 가져오기
    $sql = "SELECT NICKNAME, GROUP_CONCAT(ANSWER ORDER BY ANSWER ASC) AS ANSWERS FROM CLEARED_STAGE WHERE NICKNAME = ? GROUP BY NICKNAME";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement']);
        exit;
    }

    $stmt->bind_param("s", $nickname);
    $stmt->execute();
    $result = $stmt->get_result();

    $clearedCards = [];
    if ($row = $result->fetch_assoc()) {
        $answers = $row['ANSWERS']; // 예: "1,2,3"
        $clearedCards = array_map('intval', explode(',', $answers));
    }

    echo json_encode(['success' => true, 'clearedCards' => $clearedCards]);

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
