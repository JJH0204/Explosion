<?php
header('Content-Type: application/json');

$CORRECT_IP = "211.229.100.142";
$FLAG = "flag{find_flame_real_ip}";

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (isset($data['ip'])) {
    if ($data['ip'] === $CORRECT_IP) {
        echo json_encode([
            'success' => true,
            'message' => "축하합니다! 정확한 서버 IP를 찾았습니다!<br>Flag: $FLAG"
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => "틀린 IP 주소입니다. 다시 시도해보세요."
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => "잘못된 요청입니다."
    ]);
}
?> 