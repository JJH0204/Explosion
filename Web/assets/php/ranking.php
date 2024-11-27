<?php
/*
    ranking.php
    - 랭킹 조회 용도
*/
header('Content-Type: application/json');
$host = '127.0.0.1';
$db = 'flameDB';
$username = 'admin';
$passwd = 'flamerootpassword';
$charset = 'utf8mb4';

// 데이터베이스 연결
$conn = new mysqli($host, $username, $passwd, $db);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

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
