<?php
session_start();
header('Content-Type: application/json');

// 로그인 체크
if (!isset($_SESSION['nickname'])) {
    die(json_encode(['error' => '로그인이 필요합니다.']));
}

$host = 'localhost';
$user = $_SESSION['nickname'];
$password = 'firewalld';
$database = 'userDB';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die(json_encode(['error' => '데이터베이스 연결 실패: ' . $conn->connect_error]));
}

// SQL 쿼리 처리
$query = $_POST['query'] ?? '';

if (!empty($query)) {
    // 현재 로그인한 사용자의 테이블 이름
    $userTable = $_SESSION['user_id'];
    
    // SHOW DATABASES 쿼리 허용
    $cleanQuery = strtolower(trim(str_replace(';', '', $query)));
    if ($cleanQuery === 'show databases') {
        $result = $conn->query($query);
        if ($result) {
            $data = [];
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                if ($row[0] !== 'information_schema') {
                    $data[] = ['Database' => $row[0]];
                }
            }
            echo json_encode(['data' => $data]);
            exit;
        }
    }
    
    // SHOW TABLES 쿼리 허용
    if ($cleanQuery === 'show tables') {
        $result = $conn->query($query);
        if ($result) {
            $data = [];
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                if ($row[0] === $userTable) {
                    $data[] = ['Tables_in_userDB' => $row[0]];
                }
            }
            echo json_encode(['data' => $data]);
            exit;
        }
    }
    
    // users 테이블 접근 제한
    if (stripos($query, 'users') !== false) {
        die(json_encode(['error' => '쿼리 실행 실패: SELECT command denied to user min@localhost for table `userDB`.`users`']));
    }
    
    // 자신의 테이블만 접근 가능
    if (stripos($query, $userTable) === false && $cleanQuery !== 'show tables' && $cleanQuery !== 'show databases') {
        die(json_encode(['error' => '자신의 테이블만 조회할 수 있습니다.']));
    }
    
    $result = $conn->query($query);
    
    if ($result === TRUE) {
        echo json_encode(['success' => '쿼리가 성공적으로 실행되었습니다.']);
    } elseif ($result === FALSE) {
        echo json_encode(['error' => '쿼리 실행 실패: ' . $conn->error]);
    } else {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode(['data' => $data]);
    }
}

$conn->close();
?> 