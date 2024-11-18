<?php
session_start();

// 로그인 확인
if (!isset($_SESSION['nickname'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

if (!isset($_POST['cardId'])) {
    echo json_encode(['success' => false, 'error' => 'No card ID provided']);
    exit;
}

$nickname = $_SESSION['nickname'];
$cardId = intval($_POST['cardId']); // 카드 ID

try {
    // 데이터베이스 연결
    $conn = new mysqli('localhost', 'admin', 'flamerootpassword', 'testDB');
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
        exit;
    }

    // CLEARED_STAGE 테이블에 데이터 삽입
    $sql = "INSERT INTO CLEARED_STAGE (NICKNAME, ANSWER) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement']);
        exit;
    }

    $stmt->bind_param("si", $nickname, $cardId);
    $stmt->execute();

    echo json_encode(['success' => true]);

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
