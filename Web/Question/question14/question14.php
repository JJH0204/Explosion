<?php
header('Content-Type: application/json');

// 비밀 번호와 시도 횟수를 세션에 저장하여 유지
session_start();
if (!isset($_SESSION['secretNumber'])) {
    $_SESSION['secretNumber'] = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    $_SESSION['attempts'] = 0;
}

$secretNumber = $_SESSION['secretNumber'];
$flag = 'FLAG{number_baseball_success}';

// 시도 횟수를 증가
$_SESSION['attempts']++;

// 숫자 야구 로직 함수
function checkGuess($secret, $guess) {
    $strikes = 0;
    $balls = 0;

    // 각 숫자의 사용 여부를 추적
    $secretUsed = array_fill(0, strlen($secret), false);
    $guessUsed = array_fill(0, strlen($guess), false);

    // 1단계: 스트라이크 계산 (숫자와 위치 모두 일치)
    for ($i = 0; $i < strlen($guess); $i++) {
        if ($guess[$i] === $secret[$i]) {
            $strikes++;
            $secretUsed[$i] = true;
            $guessUsed[$i] = true;
        }
    }

    // 2단계: 볼 계산 (숫자는 일치하지만 위치가 다름)
    for ($i = 0; $i < strlen($guess); $i++) {
        if (!$guessUsed[$i]) { // 스트라이크로 사용되지 않은 경우만 확인
            for ($j = 0; $j < strlen($secret); $j++) {
                if (!$secretUsed[$j] && $guess[$i] === $secret[$j]) {
                    $balls++;
                    $secretUsed[$j] = true;
                    break;
                }
            }
        }
    }

    return ["strikes" => $strikes, "balls" => $balls];
}

$request = json_decode(file_get_contents('php://input'), true);
$guess = $request['guess'] ?? '';

// 유효성 검사
if (!preg_match('/^[0-9]{3}$/', $guess)) {
    echo json_encode([
        'status' => 'error',
        'message' => '유효한 3자리 숫자를 입력하세요.'
    ]);
    exit;
}

// 시도 횟수가 10번을 초과하면 정답을 알려주고 새 게임 시작
if ($_SESSION['attempts'] > 10) {
    $correctAnswer = $_SESSION['secretNumber'];
    $_SESSION['secretNumber'] = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    $_SESSION['attempts'] = 0;

    echo json_encode([
        'status' => 'out',
        'message' => "OUT!! 정답은 {$correctAnswer}입니다. 새 게임을 시작하세요!"
    ]);
    exit;
}

// 결과 계산
$result = checkGuess($secretNumber, $guess);

// 정답을 맞췄을 경우
if ($result['strikes'] === 3) {
    $correctAnswer = $_SESSION['secretNumber'];
    $_SESSION['secretNumber'] = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT); // 새 게임 시작
    $_SESSION['attempts'] = 0;

    echo json_encode([
        'status' => 'success',
        'flag' => $flag,
        'message' => "축하합니다! 정답({$correctAnswer})을 맞췄습니다!"
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => "{$result['strikes']} 스트라이크, {$result['balls']} 볼입니다. 남은 기회: " . (10 - $_SESSION['attempts'])
    ]);
}
?>
