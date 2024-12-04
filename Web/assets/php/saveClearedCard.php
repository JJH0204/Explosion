<?php
/*
    saveClearedCard.php
    - 클리어한 카드 저장 용도
    - 카드 클리어시 flameDB.CLEARED_STAGE 테이블에 저장
*/
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
$cardId = intval($_POST['cardId']);

try {
    // 데이터베이스 연결
    $conn = include 'config.php';

    $conn->begin_transaction();

    try {
        // 이미 클리어한 문제인지 확인
        $check_sql = "SELECT COUNT(*) as count FROM CLEARED_STAGE WHERE NICKNAME = ? AND ANSWER = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $nickname, $cardId);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            throw new Exception('Already cleared this challenge');
        }

        // CLEARED_STAGE 테이블에 데이터 삽입
        $insert_sql = "INSERT INTO CLEARED_STAGE (NICKNAME, ANSWER) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("si", $nickname, $cardId);
        $insert_stmt->execute();

        // LAB_SCORE에서 문제 점수 가져오기
        $score_sql = "SELECT SCORE FROM LAB_SCORE WHERE ID = ?";
        $score_stmt = $conn->prepare($score_sql);
        $score_stmt->bind_param("i", $cardId);
        $score_stmt->execute();
        $score_result = $score_stmt->get_result();
        $score_row = $score_result->fetch_assoc();
        $problem_score = $score_row['SCORE'];

        // USER_info 테이블 업데이트
        // 현재 점수와 스테이지 수를 가져온 후 업데이트
        $update_sql = "UPDATE USER_info 
                      SET SCORE = SCORE + ?, 
                          STAGE = (SELECT COUNT(DISTINCT ANSWER) FROM CLEARED_STAGE WHERE NICKNAME = ?) 
                      WHERE NICKNAME = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("iss", $problem_score, $nickname, $nickname);
        $update_stmt->execute();

        $conn->commit();

        // 업데이트된 정보 가져오기
        $info_sql = "SELECT SCORE, STAGE FROM USER_info WHERE NICKNAME = ?";
        $info_stmt = $conn->prepare($info_sql);
        $info_stmt->bind_param("s", $nickname);
        $info_stmt->execute();
        $info_result = $info_stmt->get_result();
        $info_row = $info_result->fetch_assoc();

        echo json_encode([
            'success' => true,
            'score' => $info_row['SCORE'],
            'stage' => $info_row['STAGE']
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
