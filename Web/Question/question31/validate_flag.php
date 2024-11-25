<?php
// 올바른 플래그 값
$correct_flag = "FLAG{cron_job_solved}";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST 데이터 가져오기
    $user_flag = trim($_POST['flag']);

    header('Content-Type: application/json');

    // 플래그 검증
    if ($user_flag === $correct_flag) {
        echo json_encode([
            'success' => true
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '플래그가 올바르지 않습니다. 다시 시도하세요.'
        ]);
    }
    exit;
}
