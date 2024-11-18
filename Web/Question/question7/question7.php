<?php
header('Content-Type: application/json');

function decodeFlag() {
    // XOR 복호화 로직을 서버 측으로 이동
    $key = 0xFF;
    $encoded = [0x7F, 0x6E, 0x61, 0x67, 0x7B, 0x6D, 0x65, 0x6D, 0x5F, 0x68, 0x61, 0x63, 0x6B, 0x7D];
    $decoded = 'FLAG_';
    foreach ($encoded as $byte) {
        $decoded .= chr($byte ^ $key);
    }
    return $decoded;
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data && isset($data['action']) && $data['action'] === 'decode_flag') {
    echo json_encode([
        'success' => true,
        'flag' => decodeFlag()
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => '잘못된 요청입니다.'
    ]);
}
?> 