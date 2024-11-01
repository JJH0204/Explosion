<?php
session_start();

// 데이터베이스 설정
$host = '127.0.0.1';
$db = 'GameScore';
$username = 'root';
$passwd = '1515';
$charset = 'utf8mb4';

// 데이터베이스 연결
$conn = new mysqli($host, $username, $passwd, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 랭킹 조회 쿼리: 점수 기준 내림차순으로 상위 10명 조회
$sql = "SELECT username, score, stage FROM Scoreboard ORDER BY score DESC LIMIT 10";
$result = $conn->query($sql);

$ranking = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ranking[] = [
            'username' => $row['username'],
            'score' => $row['score'],
            'stage' => $row['stage']
        ];
    }
}

// JSON 형식으로 랭킹 데이터 반환
echo json_encode($ranking);

// 연결 종료
$conn->close();
?>
