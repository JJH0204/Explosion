<?php
header('Content-Type: application/json');

// CORS 설정 (필요한 경우)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// 정답 전화번호 설정
$correct_number = "07082729218";  // 하이픈 제외
$flag = "FLAG{C4ll_M3_M4yB3_B4by}";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_number = isset($_POST['number']) ? $_POST['number'] : '';
    
    // 입력값에서 하이픈 제거
    $user_number = str_replace('-', '', $user_number);
    
    if ($user_number === $correct_number) {
        echo json_encode([
            'success' => true,
            'flag' => $flag
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '틀린 전화번호입니다.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => '잘못된 요청입니다.'
    ]);
}
?>
