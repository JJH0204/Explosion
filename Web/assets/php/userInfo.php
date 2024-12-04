<?php
/*
    userInfo.php
    - 유저 정보 조회 용도
    - 사이드 바 랭킹 표시 용도
*/
session_start();
header('Content-Type: application/json');

try {
    // config.php 파일로 DB 연결 시도
    $conn = include 'config.php';

    // config.php 정상 실행 여부 확인
    if (!$conn instanceof mysqli) {
        throw new Exception("Database connection was not established correctly.");
    }

    if (isset($_SESSION['id'])) {
        $id = $_SESSION['id'];

        // 먼저 전체 랭킹을 가져옴 (실시간 랭킹과 동일한 정렬 기준 사용)
        $rankQuery = "SELECT nickname, score, total_cleared_stage, recorede_date 
                     FROM USER_INFO 
                     ORDER BY score DESC, recorede_date ASC";
        
        $rankResult = $conn->query($rankQuery);
        $rank = 1;
        $userRank = 0;
        $userScore = 0;
        $userStage = 0;

        // 유저의 순위 찾기
        while($row = $rankResult->fetch_assoc()) {
            if($row['nickname'] === $nickname) {
                $userRank = $rank;
                $userScore = $row['score'];
                $userStage = $row['total_cleared_stage'];
                break;
            }
            $rank++;
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'nickname' => $nickname,
                'rank' => $userRank,
                'score' => $userScore,
                'stage' => $userStage
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'User not logged in'
        ]);
    }

    $conn->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
