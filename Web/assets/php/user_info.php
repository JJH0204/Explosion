<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$db = 'testDB';
$username = 'admin';
$password = 'flamerootpassword';

try {
    $conn = new mysqli($host, $username, $password, $db);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $user_id = $_SESSION['user_id'] ?? null;
    
    if (!$user_id) {
        throw new Exception("User not logged in");
    }

    // USER_info 테이블에서 모든 필드 조회
    $stmt = $conn->prepare("SELECT ID, NICKNAME, SCORE, STAGE FROM USER_info WHERE ID = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        
        // 랭킹 계산 (점수와 스테이지 기준 내림차순)
        $rank_query = "SELECT COUNT(*) + 1 as rank FROM USER_info WHERE SCORE > ? OR (SCORE = ? AND STAGE > ?)";
        $rank_stmt = $conn->prepare($rank_query);
        $rank_stmt->bind_param("iii", $user_data['SCORE'], $user_data['SCORE'], $user_data['STAGE']);
        $rank_stmt->execute();
        $rank_result = $rank_stmt->get_result();
        $rank = $rank_result->fetch_assoc()['rank'];

        echo json_encode([
            'success' => true,
            'data' => [
                'nickname' => $user_data['NICKNAME'],
                'score' => $user_data['SCORE'],
                'stage' => $user_data['STAGE'],
                'rank' => $rank
            ]
        ]);
    } else {
        throw new Exception("User not found");
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
