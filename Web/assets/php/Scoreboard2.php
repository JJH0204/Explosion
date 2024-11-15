<?php
session_start();

// 데이터베이스 설정
$host = '127.0.0.1';
$db = 'testDB';
$username = 'admin';
$passwd = 'flamerootpassword';
$charset = 'utf8mb4';

// 게임 내 점수 업데이트 요청인지 확인
if (!isset($_SESSION['is_game_request']) || $_SESSION['is_game_request'] !== true) {
    echo json_encode(["error" => "잘못된 접근입니다."]);
    exit;
}

// 세션에서 사용자 정보 가져오기
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['username'] ?? null;

if (!$user_id || !$user_name) {
    echo json_encode(["error" => "로그인이 필요합니다."]);
    exit;
}

// 데이터베이스 연결
$conn = new mysqli($host, $username, $passwd, $db);
if ($conn->connect_error) {
    echo json_encode(["error" => "데이터베이스 연결 실패"]);
    exit;
}

try {
    // 트랜잭션 시작
    $conn->begin_transaction();

    // 현재 사용자 정보 조회
    $stmt = $conn->prepare("SELECT SCORE, STRAGE FROM Score WHERE NICKNAME = ?");
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $new_score = $row['SCORE'] + 10;  // 10점 추가
        $new_stage = $row['STRAGE'] + 1;  // 스테이지 1 증가

        // 점수와 스테이지 업데이트
        $update_stmt = $conn->prepare("UPDATE Score SET SCORE = ?, STRAGE = ?, RECOREDE_DATE = CURRENT_TIMESTAMP WHERE NICKNAME = ?");
        $update_stmt->bind_param("iis", $new_score, $new_stage, $user_name);
        $update_stmt->execute();
    } else {
        // 새 사용자 기록 생성
        $new_score = 10;
        $new_stage = 1;
        $insert_stmt = $conn->prepare("INSERT INTO Score (NICKNAME, ID, SCORE, STRAGE) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssii", $user_name, $user_id, $new_score, $new_stage);
        $insert_stmt->execute();
    }

    // 트랜잭션 커밋
    $conn->commit();

    echo json_encode([
        "success" => true,
        "score" => $new_score,
        "stage" => $new_stage
    ]);

} catch (Exception $e) {
    // 오류 발생 시 롤백
    $conn->rollback();
    echo json_encode([
        "error" => "업데이트 실패: " . $e->getMessage()
    ]);
} finally {
    $conn->close();
    $_SESSION['is_game_request'] = false;
}
?>
