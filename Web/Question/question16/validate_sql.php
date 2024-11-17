<?php
header('Content-Type: application/json');

// 정답 SQL 명령어 (소문자로 저장)
$correct_query = "grant select, delete on user_db.test to 'test'@'localhost';";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 입력된 SQL 명령어 가져오기
    $input = json_decode(file_get_contents('php://input'), true)['query'];

    // 입력값 소문자로 변환 후 공백 제거
    $normalized_input = strtolower(trim($input));

    // 정답 비교
    if ($normalized_input === $correct_query) {
        echo json_encode([
            'status' => 'success',
            'flag' => 'FLAG{sql_grant_correct}'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'SQL 명령어가 올바르지 않습니다. 다시 시도하세요.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => '잘못된 요청 방식입니다.'
    ]);
}
?>
