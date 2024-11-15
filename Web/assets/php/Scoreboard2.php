<?php
session_start();

// 데이터베이스 설정
$host = '127.0.0.1';
$db = 'GameScore';
$username = 'admin';
$passwd = 'flamerootpassword';
$charset = 'utf8mb4';

// 게임 내 점수 업데이트 요청인지 확인하는 토큰 검사
if (!isset($_SESSION['is_game_request']) || $_SESSION['is_game_request'] !== true) {
    echo json_encode(["error" => "잘못된 접근입니다."]);
    exit;
}


// 세션에서 사용자 정보 가져오기
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['username'] ?? null;

if (!$user_id || !$user_name) {
    die("로그인 상태가 아닙니다.");
}

// 데이터베이스 연결
$conn = new mysqli($host, $username, $passwd, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 사용자 점수와 스테이지 조회
$sql_select = "SELECT score, stage FROM Scoreboard WHERE game_id = ? AND username = ?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("is", $user_id, $user_name);
$stmt_select->execute();
$result = $stmt_select->get_result();

// 점수와 스테이지 업데이트
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $current_score = $row['score'];
    $current_stage = $row['stage'];

    // 새로운 점수와 스테이지 설정
    $new_score = $current_score + 10; // 예: 10점 추가
    $new_stage = $current_stage + 1;  // 스테이지 1 증가

    // 업데이트 쿼리
    $sql_update = "UPDATE Scoreboard SET score = ?, stage = ?, recorded_at = CURRENT_TIMESTAMP WHERE game_id = ? AND username = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("iiis", $new_score, $new_stage, $user_id, $user_name);
    $stmt_update->execute();
} else {
    // 새로운 사용자 기록 추가
    $new_score = 10;
    $new_stage = 1;

    $sql_insert = "INSERT INTO Scoreboard (username, game_id, score, stage) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("siii", $user_name, $user_id, $new_score, $new_stage);
    $stmt_insert->execute();
}

// JSON 응답
echo json_encode([
    "message" => "Score updated successfully!",
    "score" => $new_score,
    "stage" => $new_stage
]);

// 연결 종료
$conn->close();

// 점수 업데이트 후 요청 완료, 세션 값 초기화
$_SESSION['is_game_request'] = false;
?>
