<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$db = 'testDB';
$username = 'admin';
$passwd = 'flamerootpassword';

try {
    $conn = new mysqli($host, $username, $passwd, $db);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
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
