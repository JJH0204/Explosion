<?php
header('Content-Type: application/json');

// JSON 데이터 받기
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// 요청 방식 확인
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method not allowed"]);
    exit;
}

// JSON 데이터에서 토큰 확인
$success_token = isset($data['token']) ? $data['token'] : '';
if ($success_token !== 'valid_game_completion') {
    http_response_code(403);
    echo json_encode(["error" => "Invalid game completion"]);
    exit;
}

$flag = "FLAG{find_the_O_success}";
echo json_encode(["flag" => $flag]);
?>
