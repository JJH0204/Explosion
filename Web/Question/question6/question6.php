<?php
header('Content-Type: application/json');

// 사용자 데이터와 플래그는 서버에서만 접근 가능
$users = [
    ['id' => 1, 'username' => 'admin', 'password' => 'admin123', 'isAdmin' => true],
    ['id' => 2, 'username' => 'user1', 'password' => 'password123', 'isAdmin' => false],
    ['id' => 3, 'username' => 'user2', 'password' => 'password456', 'isAdmin' => false]
];

$FLAG = "flag{sql_injection_success}";

// POST 데이터 받기
$input = file_get_contents("php://input");
$data = json_decode($input, true);

$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

// SQL Injection 시뮬레이션
function simulateSQL($username, $password) {
    global $users, $FLAG;
    
    // SQL Injection 취약점 시뮬레이션 로직
    // (기존 로직을 PHP로 변환)
    
    // 여기에 기존 검사 로직을 PHP로 구현
    // ...

    return [
        'success' => false,
        'message' => '로그인 실패'
    ];
}

$result = simulateSQL($username, $password);
echo json_encode($result);
?> 