<?php
// 정답과 플래그
$correctAnswers = [
    "question1" => "hosts",
    "question2" => "services",
    "question3" => "ARP"
];

$flag = "FLAG{linux_master}";

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // 답안 검증
    foreach ($data as $key => $value) {
        if (!isset($correctAnswers[$key]) || $value !== $correctAnswers[$key]) {
            echo json_encode(["status" => "error"]);
            exit;
        }
    }

    // 모든 문제 정답
    echo json_encode(["status" => "success", "flag" => $flag]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 35: Linux Master</title>
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
            max-width: 800px;
            background-color: #1e1e1e;
            border: 1px solid #00ff00;
            border-radius: 5px;
        }

        input {
            background-color: #2a2a2a;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px;
            width: 97%;
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

        #result, #flag {
            margin-top: 20px;
            font-size: 1.2em;
        }

        .question {
            margin-bottom: 30px;
            text-align: left;
            background-color: #2a2a2a;
            padding: 20px;
            border: 1px solid #00ff00;
            border-radius: 5px;
        }

        .question p {
            margin: 10px 0;
        }

        .question b {
            color: #00ff00;
        }
    </style>
</head>
<body>
    <h1>Linux Master</h1>
    <div class="container">
        <div class="question">
            <p>1. 다음 설명에 해당하는 파일명으로 알맞은 것은?</p>
            <p><b>kait라고 입력하면 ihd.or.kr 도메인이 자동으로 덧붙여지도록 특정 도메인을 등록해서 이를 호출 시 단축 하려고 한다. 예를 들면 kait를 호출하면 kait.ihd.or.kr로 접속되도록 한다. 해당하는 파일명은?</b></p>
            <input type="text" id="question1" placeholder="파일명 입력">
        </div>

        <div class="question">
            <p>2. 다음 중 네트워크 프로토콜에 할당된 포트 번호를 확인할 수 있는 파일명으로 알맞은 것은?</p>
            <input type="text" id="question2" placeholder="파일명 입력">
        </div>

        <div class="question">
            <p>3. 다음 설명에 해당하는 네트워크 프로토콜로 알맞은 것은?</p>
            <p><b>소프트웨어적으로 할당된 논리 주소인 IP 주소를 실제 물리 주소인 MAC 주소로 변환하는 역할을 수행하는 네트워크 프로토콜은?</b></p>
            <input type="text" id="question3" placeholder="프로토콜 이름 입력">
        </div>

        <button onclick="submitAnswers()">제출</button>
        <p id="result"></p>
        <p id="flag"></p>
    </div>

    <script>
        async function submitAnswers() {
            const answers = {
                question1: document.getElementById('question1').value.trim(),
                question2: document.getElementById('question2').value.trim(),
                question3: document.getElementById('question3').value.trim(),
            };

            const response = await fetch("index.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(answers),
            });

            const result = await response.json();

            const resultElement = document.getElementById('result');
            const flagElement = document.getElementById('flag');

            if (result.status === "success") {
                resultElement.style.color = '#00ff00';
                resultElement.textContent = '모든 문제를 정확히 해결했습니다!';
                flagElement.style.color = '#00ff00';
                flagElement.textContent = `플래그: ${result.flag}`;
            } else {
                resultElement.style.color = '#ff0000';
                resultElement.textContent = '틀린 답이 있습니다. 모든 문제를 맞춰야 플래그가 생성됩니다.';
                flagElement.textContent = '';
            }
        }
    </script>
</body>
</html>
