<?php
// 정답과 플래그
$correctAnswers = [
    "dllMainAddr" => "0x1000D02E",
    "dnsRequest" => "pics.practicalmalwareanalysis.com",
    "hostIndicator" => ["vmx32to64.exe", "SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\Run", "vmx32to64.exe, SOFTWARE\\Microsoft\\Windows\\CurrentVersion\\Run"],
    "networkIndicator" => "www.practicalmalwareanalysis.com"
];

$flag = "FLAG{M4lw4r3_4n4lys1s_Pr0}";

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // 답안 검증
    foreach ($data as $key => $value) {
        if ($key === "hostIndicator") {
            // hostIndicator는 세 가지 답 중 하나만 맞아도 정답
            if (!in_array($value, $correctAnswers[$key])) {
                echo json_encode(["status" => "error"]);
                exit;
            }
        } else {
            if ($value !== $correctAnswers[$key]) {
                echo json_encode(["status" => "error"]);
                exit;
            }
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
    <title>Challenge 34: Practice Malware</title>
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
            max-width: 600px;
            background-color: #1e1e1e;
            border: 1px solid #00ff00;
            border-radius: 5px;
        }

        input {
            background-color: #2a2a2a;
            border-radius: 3px;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px;
            width: 95%;
            margin-bottom: 10px;
            text-align: center;
        }

        button {
            background-color: #00ff00;
            color: #000;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 20px;
            font-weight: bold;
            border-radius: 5px;
        }

        button:hover {
            background-color: #33ff99;
        }

        #result, #flag {
            margin-top: 20px;
            font-size: 1.2em;
        }

        .question-section {
            margin: 20px 0;
            text-align: left;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>Practice Malware</h1>
    <div class="container">
        <div class="question-section">
            <h3>1. Lab05-01.dll 분석</h3>
            <p>1) DLLMain의 주소는 무엇인가?</p>
            <input type="text" id="dllMainAddr" placeholder="예: 0x10001000">
            
            <p>2) 0x10001757 주소에 위치한 gethostbyname 호출을 보면 <br>어떤 주소로 DNS 요청이 이루어지는가?</p>
            <input type="text" id="dnsRequest" placeholder="도메인 주소 입력">
        </div>

        <div class="question-section">
            <h3>2. Lab03-01.exe 분석</h3>
            <p>1) 호스트 기반 표시자는 무엇인가?</p>
            <input type="text" id="hostIndicator" placeholder="파일명 또는 레지스트리 키">
            
            <p>2) 네트워크 기반 표시자는 무엇인가?</p>
            <input type="text" id="networkIndicator" placeholder="도메인 주소 입력">
        </div>

        <button onclick="checkAnswers()">제출</button>
        <p id="result"></p>
        <p id="flag"></p>
    </div>

    <script>
        async function checkAnswers() {
            const answers = {
                dllMainAddr: document.getElementById('dllMainAddr').value.trim(),
                dnsRequest: document.getElementById('dnsRequest').value.trim(),
                hostIndicator: document.getElementById('hostIndicator').value.trim(),
                networkIndicator: document.getElementById('networkIndicator').value.trim()
            };

            const response = await fetch("question34.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(answers)
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
