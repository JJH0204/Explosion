<?php
header('Content-Type: application/json');

$secretNumber = '680';
$flag = 'FLAG{number_baseball_success}';

function checkGuess($secret, $guess) {
    $strikes = 0;
    $balls = 0;
    $secretChecked = [];
    $guessChecked = [];

    // 스트라이크 계산
    for ($i = 0; $i < strlen($guess); $i++) {
        if ($guess[$i] === $secret[$i]) {
            $strikes++;
            $secretChecked[$i] = true;
            $guessChecked[$i] = true;
        }
    }

    // 볼 계산
    for ($i = 0; $i < strlen($guess); $i++) {
        if (!isset($guessChecked[$i])) {
            for ($j = 0; $j < strlen($secret); $j++) {
                if (!isset($secretChecked[$j]) && $guess[$i] === $secret[$j]) {
                    $balls++;
                    $secretChecked[$j] = true;
                    break;
                }
            }
        }
    }

    return ["strikes" => $strikes, "balls" => $balls];
}

$request = json_decode(file_get_contents('php://input'), true);
$guess = $request['guess'] ?? '';

if (!preg_match('/^[0-9]{3}$/', $guess)) {
    echo json_encode([
        'status' => 'error',
        'message' => '유효한 3자리 숫자를 입력하세요.'
    ]);
    exit;
}

$result = checkGuess($secretNumber, $guess);

if ($result['strikes'] === 3) {
    echo json_encode([
        'status' => 'success',
        'flag' => $flag
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => "{$result['strikes']} 스트라이크, {$result['balls']} 볼입니다. 다시 시도하세요!"
    ]);
}
?>