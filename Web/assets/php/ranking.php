<?php
/*
    ranking.php
    - 랭킹 조회 용도
*/
header('Content-Type: application/json');

// 데이터베이스 연결
$conn = include 'config.php';

// USER_info 테이블에서 상위 10명을 점수 기준으로 정렬하고, 같은 점수일 경우 날짜순으로 정렬
$sql = "SELECT NICKNAME, SCORE, STAGE, RECOREDE_DATE 
        FROM USER_info 
        ORDER BY SCORE DESC, RECOREDE_DATE ASC 
        LIMIT 10";
        
$result = $conn->query($sql);

$rankings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rankings[] = [
            'nickname' => $row['NICKNAME'],
            'score' => $row['SCORE'],
            'stage' => $row['STAGE']
        ];
    }
}

echo json_encode(['success' => true, 'rankings' => $rankings]);
$conn->close();
?>
