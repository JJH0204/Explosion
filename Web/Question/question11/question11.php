<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $selectedNumbers = $input['numbers'] ?? [];

    // Validate input
    if (count($selectedNumbers) !== 6 || !validateNumbers($selectedNumbers)) {
        http_response_code(400);
        echo json_encode(['error' => '유효하지 않은 번호입니다.']);
        exit;
    }

    // 취약점: 현재 시간을 3분 단위로 나누어 시드값으로 사용
    $timeSlot = floor(time() / 180);
    mt_srand($timeSlot);

    // Generate random numbers (1-45, 6 unique numbers)
    $drawnNumbers = generateLottoNumbers();
    sort($drawnNumbers);

    // Check if numbers match
    $correct = $selectedNumbers === $drawnNumbers;

    $response = [
        'drawnNumbers' => $drawnNumbers,
        'correct' => $correct,
        'debug_time' => $timeSlot // 디버깅용 시간값 (취약점 힌트)
    ];

    if ($correct) {
        $response['flag'] = 'FLAG{L0770_PR3D1C71ON_M4573R}';
    }

    echo json_encode($response);
}

function validateNumbers($numbers) {
    if (!is_array($numbers)) return false;
    
    foreach ($numbers as $num) {
        if (!is_numeric($num) || $num < 1 || $num > 45) {
            return false;
        }
    }
    
    return count(array_unique($numbers)) === 6;
}

function generateLottoNumbers() {
    $numbers = [];
    while (count($numbers) < 6) {
        $num = mt_rand(1, 45);
        if (!in_array($num, $numbers)) {
            $numbers[] = $num;
        }
    }
    return $numbers;
}
?>
