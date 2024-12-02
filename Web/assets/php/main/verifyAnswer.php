<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['nickname']) || !isset($_POST['challengeId']) || !isset($_POST['answer'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit;
}

$nickname = $_SESSION['nickname'];
$challengeId = intval($_POST['challengeId']);
$submittedAnswer = $_POST['answer'];

try {
    // DB 연결
    $conn = new mysqli('localhost', 'db_admin', 'flamerootpassword', 'flameDB');
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // 트랜잭션 시작
    $conn->begin_transaction();

    try {
        // 이미 클리어한 문제인지 확인
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM CLEARED_STAGE WHERE NICKNAME = ? AND ANSWER = ?");
        $checkStmt->bind_param("si", $nickname, $challengeId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            throw new Exception("이미 클리어한 문제입니다.");
        }

        // 정답 검증
        $verifyStmt = $conn->prepare("SELECT FLAG, SCORE FROM LAB_SCORE WHERE ID = ?");
        $verifyStmt->bind_param("i", $challengeId);
        $verifyStmt->execute();
        $result = $verifyStmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("존재하지 않는 문제입니다.");
        }

        $challenge = $result->fetch_assoc();
        
        if ($submittedAnswer !== $challenge['FLAG']) {
            throw new Exception("오답입니다.");
        }

        // 클리어 기록 저장
        $saveStmt = $conn->prepare("INSERT INTO CLEARED_STAGE (NICKNAME, ANSWER) VALUES (?, ?)");
        $saveStmt->bind_param("si", $nickname, $challengeId);
        $saveStmt->execute();

        // 점수 업데이트
        $updateStmt = $conn->prepare("
            UPDATE USER_info 
            SET SCORE = SCORE + ?, 
                STAGE = (SELECT COUNT(DISTINCT ANSWER) FROM CLEARED_STAGE WHERE NICKNAME = ?)
            WHERE NICKNAME = ?
        ");
        $updateStmt->bind_param("iss", $challenge['SCORE'], $nickname, $nickname);
        $updateStmt->execute();

        $conn->commit();

        echo json_encode([
            'success' => true,
            'points' => $challenge['SCORE']
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?> 