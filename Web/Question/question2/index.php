<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON 데이터 받기
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    $userFlag = $data['flag'] ?? '';

    // 플래그 검증
    if ($userFlag === 'hidden_flag_123') {
        echo json_encode([
            'success' => true,
            'message' => '축하합니다! 플래그를 찾았습니다',
            'flag' => 'FLAG{source_code_analysis}'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '틀렸습니다. 다시 시도해보세요.'
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
    <title>Challenge 2: HTML source code analysis</title>
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

        .challenge-box {
            text-align: left;
            padding: 10px;
            margin: 20px 0;
            background-color: #2a2a2a;
            border-radius: 5px;
        }

        input {
            background-color: #2a2a2a;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px;
            width: 95%;
            margin-bottom: 10px;
            text-align: center;
            border-radius: 3px;
        }

        button {
            background-color: #00ff00;
            color: #000;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 20px;
            border-radius: 3px;
            font-weight: bold;
        }

        button:hover {
            background-color: #33ff99;
        }

        #message {
            margin-top: 20px;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <h1>HTML source code analysis</h1>
    <div class="container">
        <div class="challenge-box">
            <p>이 페이지에 숨겨진 플래그를 찾으세요.</p>
            <p>힌트: 페이지 소스를 확인해보세요!</p>
        </div>
        
        <!-- 힌트: flag는 'hidden_flag_123' -->
        <input type="password" id="flagInput" placeholder="플래그를 입력하세요">
        <button onclick="checkFlag()">제출</button>
        <div id="message"></div>
    </div>

    <script>
        function checkFlag() {
            const input = document.getElementById('flagInput').value;
            const messageDiv = document.getElementById('message');
            
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ flag: input })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageDiv.style.color = '#00ff00';
                    messageDiv.textContent = data.message + ': ' + data.flag;
                } else {
                    messageDiv.style.color = '#ff0000';
                    messageDiv.textContent = data.message;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.style.color = '#ff0000';
                messageDiv.textContent = '오류가 발생했습니다.';
            });
        }
    </script>
</body>
</html>
