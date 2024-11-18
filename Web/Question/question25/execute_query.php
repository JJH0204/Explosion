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

// config.json 업데이트 요청 처리
if (isset($_POST['action']) && $_POST['action'] === 'updateConfig') {
    if (isset($_POST['flag'])) {
        $newFlag = $_POST['flag'];
        $configFile = '../../data/config.json';
        
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
            
            // Challenge 25의 answer 업데이트
            foreach ($config['challenges'] as &$challenge) {
                if ($challenge['id'] === 25) {
                    $challenge['answer'] = $newFlag;
                    break;
                }
            }
            
            // 파일에 저장
            if (file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT))) {
                echo json_encode(['success' => true]);
                exit;
            }
        }
        echo json_encode(['success' => false, 'error' => 'Config 파일 업데이트 실패']);
        exit;
    }
}

// SQL 쿼리 처리
$query = $_POST['query'] ?? '';

if (!empty($query)) {
    // 현재 로그인한 사용자의 테이블 이름
    $userTable = $_SESSION['user_id'];
    
    // SHOW DATABASES 쿼리 허용 (세미콜론이 있는 경우와 없는 경우 모두 처리)
    $cleanQuery = strtolower(trim(str_replace(';', '', $query)));
    if ($cleanQuery === 'show databases') {
        $result = $conn->query($query);
        if ($result) {
            $data = [];
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                // information_schema를 제외하고 출력
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
    
    // 기존 쿼리 검증 로직
    if (stripos($query, 'users') !== false) {
        die(json_encode(['error' => '쿼리 실행 실패: SELECT command denied to user min@localhost for table `userDB`.`users`']));
    }
    
    // 쿼리에 현재 사용자의 테이블 이름이 포함되어 있는지 확인 (SHOW TABLES는 제외)
    if (stripos($query, $userTable) === false && $cleanQuery !== 'show tables' && $cleanQuery !== 'show databases') {
        die(json_encode(['error' => '자신의 테이블만 조회할 수 있습니다.']));
    }
    
    $result = $conn->query($query);
    
    if ($result === TRUE) {
        // UPDATE 쿼리인 경우 FLAG 값을 확인하고 config.json 업데이트
        if (stripos($query, 'UPDATE') !== false && stripos($query, 'FLAG') !== false) {
            // FLAG 값 추출을 위한 SELECT 쿼리 실행
            $selectResult = $conn->query("SELECT FLAG FROM $userTable");
            if ($selectResult && $row = $selectResult->fetch_assoc()) {
                $newFlag = $row['FLAG'];
                
                // config.json 파일 업데이트
                $configFile = '../../data/config.json';
                if (file_exists($configFile)) {
                    $config = json_decode(file_get_contents($configFile), true);
                    
                    // Challenge 25의 answer 업데이트
                    foreach ($config['challenges'] as &$challenge) {
                        if ($challenge['id'] === 25) {
                            $challenge['answer'] = $newFlag;
                            break;
                        }
                    }
                    
                    // 파일에 저장
                    file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));
                }
            }
        }
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