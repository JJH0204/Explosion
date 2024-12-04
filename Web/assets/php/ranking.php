<?php
/*
    ranking.php
    - 랭킹 조회 용도
*/
header('Content-Type: application/json');

// 데이터베이스 연결
$conn = include 'config.php';

// USER_info 테이블에서 상위 10명을 점수 기준으로 정렬하고, 같은 점수일 경우 날짜순으로 정렬
$sql = "SELECT u.id, i.nickname, u.score, u.recorede_date 
        FROM USER_INFO u
        JOIN ID_INFO i ON u.id = i.id 
        ORDER BY u.score DESC, u.recorede_date ASC 
        LIMIT 10";
        
$result = $conn->query($sql);

$rankings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rankings[] = [
            'nickname' => $row['nickname'],
            'score' => $row['score']
        ];
    }
}

echo json_encode(['success' => true, 'rankings' => $rankings]);
$conn->close();
?>
