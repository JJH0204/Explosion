<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

$host = '127.0.0.1';
$db = 'testDB';
$username = 'admin';
$passwd = 'flamerootpassword';
$charset = 'utf8mb4';

$response = ["success" => false];

try {
    if (!isset($_SESSION['is_game_request']) || $_SESSION['is_game_request'] !== true) {
        throw new Exception("잘못된 접근입니다.");
    }

    $user_id = $_SESSION['user_id'] ?? null;
    $user_nickname = $_SESSION['nickname'] ?? null;

    if (!$user_id || !$user_nickname) {
        throw new Exception("로그인이 필요합니다.");
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli($host, $username, $passwd, $db);
    $conn->set_charset($charset);

    $conn->begin_transaction();

    try {
        // CLEARED_STAGE에서 클리어한 스테이지 수 계산
        $cleared_stmt = $conn->prepare("SELECT COUNT(DISTINCT ANSWER) as completed_count FROM CLEARED_STAGE WHERE NICKNAME = ?");
        $cleared_stmt->bind_param("s", $user_nickname);
        $cleared_stmt->execute();
        $cleared_result = $cleared_stmt->get_result();
        $completed_count = 0;
        
        if ($cleared_row = $cleared_result->fetch_assoc()) {
            $completed_count = $cleared_row['completed_count'];
        }

        // 클리어한 스테이지들의 총 점수 계산
        $score_stmt = $conn->prepare("
            SELECT COALESCE(SUM(ls.SCORE), 0) as total_score 
            FROM CLEARED_STAGE cs 
            JOIN LAB_SCORE ls ON cs.ANSWER = ls.ID 
            WHERE cs.NICKNAME = ?
        ");
        $score_stmt->bind_param("s", $user_nickname);
        $score_stmt->execute();
        $score_result = $score_stmt->get_result();
        $total_score = 0;

        if ($score_row = $score_result->fetch_assoc()) {
            $total_score = $score_row['total_score'];
        }

        // USER_info 테이블 업데이트
        $update_stmt = $conn->prepare("UPDATE USER_info SET SCORE = ?, STAGE = ? WHERE ID = ?");
        $update_stmt->bind_param("iis", $total_score, $completed_count, $user_id);
        $update_stmt->execute();

        $conn->commit();

        $response["success"] = true;
        $response["score"] = $total_score;
        $response["stage"] = $completed_count;
        $response["completed_challenges"] = $completed_count;

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    $response["error"] = $e->getMessage();
    error_log("Error in Scoreboard2.php: " . $e->getMessage());
}

echo json_encode($response);
?>