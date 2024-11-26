<?php
header('Content-Type: application/json');

// POST 요청 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid method']));
}

// 제출된 플래그 가져오기
$submitted_flag = $_POST['flag'] ?? '';

// 정답 플래그
$correct_flag = "Jpnl123$Kech_Secret";
$final_flag = "FLAG{decoder_challenge_success}";

// 플래그 검증
if (trim($submitted_flag) === $correct_flag) {
    echo json_encode([
        'success' => true,
        'message' => '정답입니다!',
        'flag' => $final_flag
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => '틀렸습니다. 다시 시도하세요.'
    ]);
}
?>
