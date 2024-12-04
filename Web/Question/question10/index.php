<?php
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
    exit;
} 
?> 

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 10: Server IP Finder</title>
    <style>
        body {
            background-color: #121212;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            text-align: center;
            padding: 20px;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
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
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px;
            width: 95%;
            margin: 20px 0 10px 0;
            text-align: center;
            box-sizing: border-box;
            border-radius: 3px;
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

        .hint {
            text-align: left;
            padding: 15px;
            margin: 20px 0;
            background-color: #2a2a2a;
            border-radius: 5px;
            border: 1px solid #00ff00;
        }

        #result {
            margin-top: 20px;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Server IP Finder Challenge</h2>
        <p>이 웹 서버의 실제 IP 주소를 찾아보세요!</p>
        
        <form id="ipForm" onsubmit="return false;">
            <input type="text" id="ipInput" placeholder="서버 IP를 입력하세요" 
                   pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                   title="유효한 IPv4 주소를 입력하세요">
            <br>
            <button type="button" onclick="checkIP()">확인</button>
        </form>
        
        <p id="result"></p>
        
        <div class="hint">
            <ul style="text-align: left;">
                <li>cmd나 powershell을 사용해보세요.</li>
                <li>ping, tracert, nslookup 등의 네트워크 도구를 활용해보세요.</li>
                <li>DNS 조회 결과를 확인해보세요.</li>
            </ul>
        </div>
    </div>

    <script>
        function checkIP() {
            const input = document.getElementById("ipInput").value;
            const result = document.getElementById("result");
            
            // IP 형식 검증을 위한 정규식
            const ipRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            
            if (!ipRegex.test(input)) {
                result.className = "error";
                result.textContent = "올바른 IP 주소 형식이 아닙니다.";
                return;
            }

            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ip: input })
            })
            .then(response => response.json())
            .then(data => {
                result.className = data.success ? "" : "error";
                result.innerHTML = data.message;
            })
            .catch(error => {
                result.className = "error";
                result.textContent = "서버 오류가 발생했습니다.";
            });
        }
    </script>
</body>
</html>
