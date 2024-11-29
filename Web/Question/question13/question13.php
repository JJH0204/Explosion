<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$input = $data['input'] ?? '';

// 정확히 32바이트 + "BOFB" 패턴 검증
if (strlen($input) === 36 && substr($input, 32, 4) === "BOFB") {
    echo json_encode([
        'status' => 'success',
        'flag' => 'FLAG{advanced_buffer_overflow}'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => '버퍼 오버플로우 공격이 실패했습니다.'
    ]);
}
?>
