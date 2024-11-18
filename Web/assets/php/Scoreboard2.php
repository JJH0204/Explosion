<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 데이터베이스 설정
$host = '127.0.0.1';
$db = 'testDB';
$username = 'admin';
$passwd = 'flamerootpassword';
$charset = 'utf8mb4';

$response = ["success" => false];

// 게임 내 점수 업데이트 요청인지 확인
if (!isset($_SESSION['is_game_request']) || $_SESSION['is_game_request'] !== true) {
    $response["error"] = "잘못된 접근입니다.";
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// 세션에서 사용자 정보 가져오기
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['nickname'] ?? null;

if (!$user_id || !$user_name) {
    $response["error"] = "로그인이 필요합니다.";
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// 데이터베이스 연결
$conn = new mysqli($host, $username, $passwd, $db);
if ($conn->connect_error) {
    $response["error"] = "데이터베이스 연결 실패: " . $conn->connect_error;
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    // 트랜잭션 시작
    $conn->begin_transaction();

    // 현재 사용자 정보 조회
    $stmt = $conn->prepare("SELECT SCORE, STAGE FROM SCORE WHERE NICKNAME = ?");
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_score = $row['SCORE'];
        $current_stage = $row['STAGE'];
        $new_stage = $current_stage + 1;

        // LAB_SCORE 테이블에서 새로운 스테이지의 점수 가져오기
        $score_stmt = $conn->prepare("SELECT SCORE FROM LAB_SCORE WHERE ID = ?");
        $score_stmt->bind_param("i", $new_stage);
        $score_stmt->execute();
        $score_result = $score_stmt->get_result();
        $score_row = $score_result->fetch_assoc();
        $new_score = $current_score + $score_row['SCORE'];

        // 점수와 스테이지 업데이트
        $update_stmt = $conn->prepare("UPDATE SCORE SET SCORE = ?, STAGE = ?, RECORDED_DATE = CURRENT_TIMESTAMP WHERE NICKNAME = ?");
        $update_stmt->bind_param("iis", $new_score, $new_stage, $user_name);
        $update_stmt->execute();
    } else {
        // 새 사용자는 첫 스테이지(ID=1)의 점수로 시작
        $score_stmt = $conn->prepare("SELECT SCORE FROM LAB_SCORE WHERE ID = 1");
        $score_stmt->execute();
        $score_result = $score_stmt->get_result();
        $score_row = $score_result->fetch_assoc();
        $new_score = $score_row['SCORE'];
        $new_stage = 1;

        $insert_stmt = $conn->prepare("INSERT INTO SCORE (NICKNAME, ID, SCORE, STAGE) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssii", $user_name, $user_id, $new_score, $new_stage);
        $insert_stmt->execute();
    }

    // 트랜잭션 커밋
    $conn->commit();

    $response = [
        "success" => true,
        "score" => $new_score,
        "stage" => $new_stage
    ];

} catch (Exception $e) {
    // 오류 발생 시 롤백
    $conn->rollback();
    $response["error"] = "업데이트 실패: " . $e->getMessage();
} finally {
    $conn->close();
    $_SESSION['is_game_request'] = false;
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
?>