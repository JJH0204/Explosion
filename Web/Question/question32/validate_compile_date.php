<?php
header('Content-Type: application/json');

// 정답인 컴파일 날짜
$correct_date = '2011/10/20';

$data = json_decode(file_get_contents('php://input'), true);
$user_date = $data['date'];

if ($user_date === $correct_date) {
    echo json_encode([
        "status" => "success",
        "flag" => "FLAG{COMPILE_DATE_CORRECT}"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "컴파일 날짜가 올바르지 않습니다."
    ]);
}
?>
