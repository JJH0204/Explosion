<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 디버깅을 위한 로그 추가
error_log("Session data: " . print_r($_SESSION, true));

$host = 'localhost';
$db = 'testDB';
$user = 'admin';
$pass = 'flamerootpassword';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// user_id와 username 모두 확인
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $user_id = $_SESSION['user_id'];
    $nickname = $_SESSION['username'];
    
    // Score 테이블에서 사용자 정보 조회
    $stmt = $conn->prepare("SELECT NICKNAME, SCORE, STRAGE FROM Score WHERE NICKNAME = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        die(json_encode(['success' => false, 'error' => 'Query prepare failed']));
    }

    $stmt->bind_param("s", $nickname);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
    } else {
        // Score 테이블에 사용자 정보가 없으면 새로 추가
        $insert_stmt = $conn->prepare("INSERT INTO Score (NICKNAME, SCORE, STRAGE) VALUES (?, 0, 1)");
        $insert_stmt->bind_param("s", $nickname);
        $insert_stmt->execute();
        
        $user_data = [
            'NICKNAME' => $nickname,
            'SCORE' => 0,
            'STRAGE' => 1
        ];
    }
    
    // 전체 랭킹에서 사용자의 순위 계산
    $rank_query = "SELECT COUNT(*) + 1 as rank FROM Score WHERE SCORE > ?";
    $rank_stmt = $conn->prepare($rank_query);
    $rank_stmt->bind_param("i", $user_data['SCORE']);
    $rank_stmt->execute();
    $rank_result = $rank_stmt->get_result();
    $rank_data = $rank_result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'nickname' => $user_data['NICKNAME'],
            'score' => $user_data['SCORE'],
            'stage' => $user_data['STRAGE'],
            'rank' => $rank_data['rank']
        ]
    ]);
    
    error_log("Sent user data: " . print_r($user_data, true));
} else {
    echo json_encode([
        'success' => false,
        'error' => 'User not logged in',
        'session_data' => $_SESSION
    ]);
}

$conn->close();
?>
