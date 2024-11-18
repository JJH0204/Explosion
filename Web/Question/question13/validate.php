<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 클라이언트에서 받은 데이터
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    $userInput = $data['input'] ?? '';

    // 조건: 길이 32 초과 및 특정 패턴 포함
    if (strlen($userInput) > 32 && strpos($userInput, "BOFB") !== false) {
        echo json_encode(["status" => "success", "flag" => "FLAG{advanced_buffer_overflow}"]);
    } else {
        echo json_encode(["status" => "error", "message" => "조건을 만족하지 못했습니다."]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "잘못된 요청 방식입니다."]);
}
?>
