<?php
header('Content-Type: application/json');
$host = '127.0.0.1';
$db = 'testDB';
$username = 'admin';
$passwd = 'flamerootpassword';
$charset = 'utf8mb4';

// 데이터베이스 연결
$conn = new mysqli($host, $username, $passwd, $db);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

// USER_info 테이블에서 상위 10명을 점수와 스테이지 기준으로 정렬
$sql = "SELECT NICKNAME, SCORE, STAGE FROM USER_info ORDER BY SCORE DESC, STAGE DESC LIMIT 10";
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
