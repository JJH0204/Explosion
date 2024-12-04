<?php
session_start();
if (!isset($_SESSION['secretNumber'])) {
    $_SESSION['secretNumber'] = generateUniqueNumber();
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

// 중복되지 않는 3자리 숫자 생성 함수
function generateUniqueNumber() {
    $numbers = [];
    while (count($numbers) < 3) {
        $num = rand(0, 9);
        if (!in_array($num, $numbers)) {
            $numbers[] = $num;
        }
    }
    return implode('', $numbers);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request = json_decode(file_get_contents('php://input'), true);
    $guess = $request['guess'] ?? '';

    // 유효성 검사
    if (!preg_match('/^[0-9]{3}$/', $guess) || count(array_unique(str_split($guess))) < 3) {
        echo json_encode([
            'status' => 'error',
            'message' => '유효한 3자리 숫자를 입력하세요. (중복된 숫자는 안 됩니다.)'
        ]);
        exit;
    }

    // 시도 횟수가 10번을 초과하면 정답을 알려주고 새 게임 시작
    if ($_SESSION['attempts'] > 10) {
        $correctAnswer = $_SESSION['secretNumber'];
        $_SESSION['secretNumber'] = generateUniqueNumber();
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
        $_SESSION['secretNumber'] = generateUniqueNumber(); // 새 게임 시작
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
    exit;
}
?> 

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 14: Number Baseball Game</title>
    <style>
        body {
            background-color: #121212;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            text-align: center;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            margin: 40px auto 0;
            padding: 20px;
            max-width: 600px;
            background-color: #1e1e1e;
            border: 1px solid #00ff00;
            border-radius: 5px;
        }

        input {
            background-color: #2a2a2a;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px;
            width: 80%;
            margin: 20px 0 10px 0;
            text-align: center;
            border-radius: 3px;
            font-weight: bold;
        }

        button {
            background-color: #00ff00;
            color: #000;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 20px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            border-radius: 5px;
        }

        button:hover {
            background-color: #33ff99;
        }

        #result {
            margin-top: 10px;
            font-size: 1.2em;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>Number Baseball Game</h1>
    <div class="container">
        <p>3자리 숫자를 입력하고 정답을 맞춰보세요!</p>
        <input type="text" id="guessInput" placeholder="숫자를 입력하세요 (예: 123)">
        <button onclick="makeGuess()">제출</button>
        <div id="result"></div>
    </div>

    <script>
        async function makeGuess() {
            const guessInput = document.getElementById('guessInput').value;
            const resultDiv = document.getElementById('result');

            try {
                const response = await fetch('index.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ guess: guessInput })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    resultDiv.style.color = '#00ff00';
                    resultDiv.textContent = `홈런!!! 플래그: ${data.flag}`;
                } else {
                    resultDiv.style.color = '#ff0000';
                    resultDiv.textContent = data.message;
                }
            } catch (error) {
                resultDiv.style.color = '#ff0000';
                resultDiv.textContent = '오류 발생: 서버에 연결할 수 없습니다.';
            }
        }
    </script>
</body>
</html>