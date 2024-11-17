<?php
header('Content-Type: application/json');

// 데이터베이스 연결 설정
$host = 'localhost'; // 데이터베이스 호스트
$dbname = 'testDB'; // 데이터베이스 이름
$username = 'root'; // 데이터베이스 사용자명
$password = '1234'; // 데이터베이스 비밀번호

try {
    // 데이터베이스 연결
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // POST 요청에서 SQL 명령어 추출
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = trim($data['sql']);

    // SQL 명령어 분석
    if (preg_match("/^GRANT\s+SELECT,\s+DELETE\s+ON\s+testDB\.USER_info\s+TO\s+'([\w]+)';$/i", $sql, $matches)) {
        $user = strtolower($matches[1]); // 사용자 이름 추출 후 소문자로 변환

        // USER_info 테이블에서 사용자 확인
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM USER_info WHERE username = :username");
        $stmt->execute(['username' => $user]);
        $userExists = $stmt->fetchColumn();

        if ($userExists) {
            echo json_encode([
                'status' => 'success',
                'message' => "축하합니다! 사용자 '{$user}'에 대한 권한 부여가 성공적으로 처리되었습니다.",
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => "지정된 사용자 '{$user}'는 USER_info 테이블에 존재하지 않습니다."
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => "SQL 명령어 형식이 잘못되었습니다. 다시 확인하세요."
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => "데이터베이스 오류: " . $e->getMessage()
    ]);
}
?>
