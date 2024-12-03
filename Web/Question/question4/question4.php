<?php
// header('Content-Type: application/json');

// POST 데이터 받기
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data && isset($data['role'])) {
    if ($data['role'] === 'admin') {
        echo json_encode([
            'success' => true,
            'message' => '관리자로 로그인 성공!',
            'flag' => 'FLAG{proxy_role_manipulation_success}'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '일반 사용자로 로그인됨'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => '잘못된 요청입니다.'
    ]);
}
?> 