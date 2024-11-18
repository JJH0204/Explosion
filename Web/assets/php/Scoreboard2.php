<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

// 데이터베이스 설정
$host = '127.0.0.1';
$db = 'testDB';
$username = 'admin';
$passwd = 'flamerootpassword';
$charset = 'utf8mb4';

$response = ["success" => false];

try {
    // 게임 내 점수 업데이트 요청인지 확인
    if (!isset($_SESSION['is_game_request']) || $_SESSION['is_game_request'] !== true) {
        throw new Exception("잘못된 접근입니다.");
    }

    // 세션에서 사용자 정보 가져오기
    $user_id = $_SESSION['user_id'] ?? null;
    $user_nickname = $_SESSION['nickname'] ?? null;

    if (!$user_id || !$user_nickname) {
        throw new Exception("로그인이 필요합니다.");
    }

    // 데이터베이스 연결
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // 에러 리포팅 활성화
    $conn = new mysqli($host, $username, $passwd, $db);
    $conn->set_charset($charset);

    // 트랜잭션 시작
    $conn->begin_transaction();

    // 현재 사용자 정보 조회
    $stmt = $conn->prepare("SELECT SCORE, STAGE FROM SCORE WHERE NICKNAME = ?");
    if (!$stmt) {
        throw new Exception("Statement preparation failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $user_nickname);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_score = $row['SCORE'];
        $current_stage = $row['STAGE'];

        // CLEARED_STAGE 테이블에서 클리어한 스테이지 수 가져오기
        $cleared_stmt = $conn->prepare("SELECT ANSWER FROM CLEARED_STAGE WHERE NICKNAME = ?");
        $cleared_stmt->bind_param("s", $user_nickname);
        $cleared_stmt->execute();
        $cleared_result = $cleared_stmt->get_result();

        $cleared_stages = [];

        while ($cleared_row = $cleared_result->fetch_assoc()) {
            $cleared_stages[] = $cleared_row['ANSWER'];
        }

        if (empty($cleared_stages)) {
            $max_stage = $current_stage;
        } else {
            $max_stage = max($cleared_stages);
        }

        // LAB_SCORE 테이블에서 해당 스테이지의 점수 가져오기
        $lab_score_stmt = $conn->prepare("SELECT SCORE FROM LAB_SCORE WHERE ID = ?");
        $lab_score_stmt->bind_param("i", $max_stage);
        $lab_score_stmt->execute();
        $lab_score_result = $lab_score_stmt->get_result();
        
        $lab_score = 0; // 기본값 설정
        if ($lab_score_row = $lab_score_result->fetch_assoc()) {
            $lab_score = $lab_score_row['SCORE'];
        }

        // 새로운 점수 계산
        $new_score = $current_score + $lab_score;

        // 점수와 스테이지 업데이트
        $update_stmt = $conn->prepare("UPDATE SCORE SET SCORE = ?, STAGE = ? WHERE NICKNAME = ?");
        $update_stmt->bind_param("iis", $new_score, $max_stage, $user_nickname);
        $update_stmt->execute();

        // lab_score_stmt 닫기 추가
        $lab_score_stmt->close();
    } else {
        // 새 사용자 처리
        $new_score = 0;
        $max_stage = 1;

        $insert_stmt = $conn->prepare("INSERT INTO SCORE (NICKNAME, ID, SCORE, STAGE) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssii", $user_nickname, $user_id, $new_score, $max_stage);
        $insert_stmt->execute();
    }

    // 트랜잭션 커밋
    $conn->commit();

    // 클리어한 문제 수 계산을 위한 쿼리 추가
    $completed_stmt = $conn->prepare("SELECT COUNT(DISTINCT ANSWER) as completed_count FROM CLEARED_STAGE WHERE NICKNAME = ?");
    $completed_stmt->bind_param("s", $user_nickname);
    $completed_stmt->execute();
    $completed_result = $completed_stmt->get_result();
    $completed_count = 0;
    
    if ($completed_row = $completed_result->fetch_assoc()) {
        $completed_count = $completed_row['completed_count'];
    }

    $response["success"] = true;
    $response["score"] = $new_score;
    $response["stage"] = $max_stage;
    $response["completed_challenges"] = $completed_count; // 클리어한 문제 수 추가

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    $response["error"] = $e->getMessage();
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($cleared_stmt)) $cleared_stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($insert_stmt)) $insert_stmt->close();
    if (isset($completed_stmt)) $completed_stmt->close(); // completed_stmt 닫기 추가
    if (isset($conn)) $conn->close();
    
    $_SESSION['is_game_request'] = false;
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>