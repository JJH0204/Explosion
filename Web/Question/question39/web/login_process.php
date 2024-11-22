<?php
header('Content-Type: application/json');

// 데이터베이스 연결 설정
$host = 'localhost';
$dbname = 'fakerDB';
$username = 'faker';
$password = 'F4k3r_1s_H4rd_T0_Gu3ss';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // POST 데이터 받기
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';
    
    // 사용자 검증
    $stmt = $pdo->prepare("SELECT * FROM USER_info WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && $password === $user['password']) {
        // 로그인 성공
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        echo json_encode([
            'success' => true,
            'message' => '로그인 성공'
        ]);
    } else {
        // 로그인 실패
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => '아이디 또는 비밀번호가 올바르지 않습니다.'
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '서버 오류가 발생했습니다.'
    ]);
}
?>
