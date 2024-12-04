<?php
/*
    user_info.php
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

    if (isset($_SESSION['nickname'])) {
        $nickname = $_SESSION['nickname'];

        // 먼저 전체 랭킹을 가져옴 (실시간 랭킹과 동일한 정렬 기준 사용)
        $rankQuery = "SELECT NICKNAME, SCORE, STAGE, RECOREDE_DATE 
                     FROM USER_info 
                     ORDER BY SCORE DESC, RECOREDE_DATE ASC";
        
        $rankResult = $conn->query($rankQuery);
        $rank = 1;
        $userRank = 0;
        $userScore = 0;
        $userStage = 0;

        // 유저의 순위 찾기
        while($row = $rankResult->fetch_assoc()) {
            if($row['NICKNAME'] === $nickname) {
                $userRank = $rank;
                $userScore = $row['SCORE'];
                $userStage = $row['STAGE'];
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
