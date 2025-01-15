<?php
// 정답인 컴파일 날짜
$correct_date = '2011/10/20';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'index.php') === false) {
        http_response_code(403);
        die(json_encode(['error' => 'Access Denied']));
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $user_date = $data['date'];

    if ($user_date === $correct_date) {
        echo json_encode([
            "status" => "success",
            "flag" => "FLAG{COMPILE_DATE_CORRECT}"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "컴파일 날짜가 올바르지 않습니다."
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
    <title>Challenge 32: Malware Analysis</title>
    <style>
        body {
            background-color: #121212;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            text-align: center;
            padding: 20px;
        }

        .container {
            margin: 0 auto;
            padding: 20px;
            max-width: 700px;
            background-color: #1e1e1e;
            border: 1px solid #00ff00;
            border-radius: 5px;
        }

        input {
            background-color: #2a2a2a;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px;
            width: 30%;
            margin: 10px auto;
            border-radius: 3px;
            text-align: center;
        }

        button {
            background-color: #00ff00;
            color: #000;
            border: 1px solid #00ff00;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
        }

        button:hover {
            background-color: #33ff99;
        }

        #result {
            margin-top: 20px;
            font-size: 1.2em;
        }

        .instruction {
            text-align: left;
            padding: 20px;
            margin: 20px auto;
            background-color: #2a2a2a;
            border: 1px solid #00ff00;
            border-radius: 5px;
        }

        #flag {
            margin-top: 20px;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <h1>Malware Analysis</h1>
    <div class="container">
        <p>
            FLARE 환경에서 제공된 파일 <b>Lab16-01.exe</b>의 컴파일 날짜를 확인하세요.<br>
            확인된 컴파일 날짜를 아래 입력란에 입력하고 제출하세요.
        </p>

        <div class="instruction">
            <ul>
                <li>FLARE 환경에서 <b>Lab16-01.exe</b> 파일을 분석합니다.</li>
                <li>파일의 컴파일 날짜를 찾기 위한 적절한 도구와 방법을 스스로 탐색하세요.</li>
                <li>컴파일 날짜를 정확히 입력하세요 (예: 20xx/xx/xx).</li>
            </ul>
        </div>

        <input type="text" id="compileDate" placeholder="컴파일 날짜 입력 (예: 20xx/xx/xx)">
        <button onclick="checkDate()">제출</button>
        <p id="result"></p>
        <p id="flag"></p>
    </div>

    <script>
        async function checkDate() {
            const date = document.getElementById('compileDate').value.trim();
            const response = await fetch("index.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ date })
            });

            const result = await response.json();
            const resultElement = document.getElementById('result');
            const flagElement = document.getElementById('flag');

            if (result.status === "success") {
                resultElement.style.color = '#00ff00';
                resultElement.textContent = '정답입니다!';
                flagElement.style.color = '#00ff00';
                flagElement.textContent = `플래그: ${result.flag}`;
            } else {
                resultElement.style.color = '#ff0000';
                resultElement.textContent = '틀렸습니다. 다시 시도하세요.';
                flagElement.textContent = '';
            }
        }
    </script>
</body>
</html>