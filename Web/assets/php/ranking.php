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

// 상위 랭킹 10명의 사용자 정보를 가져오는 SQL 쿼리
$sql = "SELECT NICKNAME, SCORE, STRAGE FROM Score ORDER BY SCORE DESC, STRAGE DESC LIMIT 10";
$result = $conn->query($sql);

$rankings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rankings[] = [
            'username' => $row['NICKNAME'],  // NICKNAME 필드 사용
            'score' => $row['SCORE'],        // SCORE 필드 사용
            'stage' => $row['STRAGE']        // STRAGE 필드 사용
        ];
    }
}

echo json_encode(['success' => true, 'rankings' => $rankings]);
$conn->close();
?>
