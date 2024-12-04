<?php
// 정답과 플래그
$correctAnswers = [
    "subnet1" => "192.168.1.0-192.168.1.63",
    "subnet2" => "192.168.1.64-192.168.1.127",
    "subnet3" => "192.168.1.128-192.168.1.191",
    "subnet4" => "192.168.1.192-192.168.1.255",
    "host1" => "192.168.1.1-192.168.1.62",
    "host2" => "192.168.1.65-192.168.1.126",
    "host3" => "192.168.1.129-192.168.1.190",
    "host4" => "192.168.1.193-192.168.1.254"
];

$flag = "FLAG{SUBNETTING_MASTER}";

// 클라이언트 데이터 가져오기
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // 정답 검증
    foreach ($correctAnswers as $key => $value) {
        if (!isset($data[$key]) || $data[$key] !== $value) {
            echo json_encode(["success" => false]);
            exit;
        }
    }

    // 모든 정답이 맞으면 플래그 반환
    echo json_encode(["success" => true, "flag" => $flag]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 18: Basic Subnetting</title>
    <style>
        body {
            background-color: #121212;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            text-align: center;
            padding: 20px;
            margin: 0;
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
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .group {
            margin-bottom: 20px;
            flex-direction: column;
            align-items: center;
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

        #result {
            margin-top: 20px;
            font-size: 1.2em;
        }

        h3 {
            margin: 30px 0 15px 0;
        }
    </style>
</head>
<body>
    <h1>Basic Subnetting</h1>
    <div class="container">
        <p>네트워크 <b>192.168.1.0/24</b>을 동일한 크기의 4개 서브넷으로 나누세요.</p>

        <h3>문제 1</h3>
        <p>각 서브넷의 네트워크 주소와 브로드캐스트 주소를 입력하세요.</p>
        <div class="group">
            <input type="text" id="subnet1" placeholder="Subnet 1: 네트워크-브로드캐스트">
            <input type="text" id="subnet2" placeholder="Subnet 2: 네트워크-브로드캐스트">
            <input type="text" id="subnet3" placeholder="Subnet 3: 네트워크-브로드캐스트">
            <input type="text" id="subnet4" placeholder="Subnet 4: 네트워크-브로드캐스트">
        </div>

        <h3>문제 2</h3>
        <p>각 서브넷에서 사용할 수 있는 첫 번째와 마지막 호스트 IP를 입력하세요.</p>
        <div class="group">
            <input type="text" id="host1" placeholder="Subnet 1: 첫 번째-마지막">
            <input type="text" id="host2" placeholder="Subnet 2: 첫 번째-마지막">
            <input type="text" id="host3" placeholder="Subnet 3: 첫 번째-마지막">
            <input type="text" id="host4" placeholder="Subnet 4: 첫 번째-마지막">
        </div>

        <button onclick="submitAnswers()">제출</button>
        <p id="result"></p>
    </div>

    <script>
        async function submitAnswers() {
            const data = {
                subnet1: document.getElementById('subnet1').value.trim(),
                subnet2: document.getElementById('subnet2').value.trim(),
                subnet3: document.getElementById('subnet3').value.trim(),
                subnet4: document.getElementById('subnet4').value.trim(),
                host1: document.getElementById('host1').value.trim(),
                host2: document.getElementById('host2').value.trim(),
                host3: document.getElementById('host3').value.trim(),
                host4: document.getElementById('host4').value.trim()
            };

            const response = await fetch("index.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(data)
            });

            const result = await response.json();
            const resultElement = document.getElementById('result');

            if (result.success) {
                resultElement.style.color = "#00ff00";
                resultElement.textContent = `정답입니다! 플래그: ${result.flag}`;
            } else {
                resultElement.style.color = "#ff0000";
                resultElement.textContent = "틀렸습니다. 다시 시도해 보세요.";
            }
        }
    </script>
</body>
</html>
