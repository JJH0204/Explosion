<?php
header('Content-Type: application/json');

// 요청이 올바른 출처에서 왔는지 확인
if (!isset($_SERVER['HTTP_REFERER']) || 
    strpos($_SERVER['HTTP_REFERER'], 'question29.html') === false) {
    http_response_code(403);
    die(json_encode(['error' => 'Access Denied']));
}

// 점수가 1000점 이상인 경우에만 flag 반환
if (isset($_POST['score']) && intval($_POST['score']) >= 1000) {
    echo json_encode([
        'success' => true,
        'flag' => 'flag{4368616c6c656e6765666c6167}'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => '1000점 이상 획득해야 합니다.'
    ]);
}
?> 