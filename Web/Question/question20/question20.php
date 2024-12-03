<?php
// 올바른 플래그 값
$correct_flag = "FLAG{ASCIIcode}";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST 데이터 가져오기
    $user_flag = trim($_POST['flag']);

    header('Content-Type: application/json');

    // 플래그 검증
    if ($user_flag === $correct_flag) {
        echo json_encode([
            'success' => true
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '플래그가 올바르지 않습니다. 다시 시도하세요.'
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
    <title>Challenge 20: Binary Code</title>
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
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px;
            width: 80%;
            margin: 20px 0 10px 0;
            border-radius: 3px;
            text-align: center;
        }

        button {
            background-color: #00ff00;
            color: #000;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
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

        .hint {
            margin-top: 20px;
            font-size: 0.9em;
            color: #aaaaaa;
            background-color: #2a2a2a;
            padding: 10px;
            border-radius: 5px;
        }

        .ssh-info {
            margin: 20px auto;
            padding: 20px;
            background-color: #2a2a2a;
            border: 1px solid #00ff00;
            border-radius: 5px;
            max-width: 600px;
        }
        
        .ssh-info h3 {
            color: #00ff00;
            margin-top: 0;
        }

        .ssh-info p {
            margin: 10px 0;
        }

        .ssh-info span {
            color: #00ff00;
        }
    </style>
</head>
<body>
    <h1>Binary Code</h1>
    <div class="container">
        <p>SSH 접속 후 올바른 플래그를 찾아 제출하세요.</p>

        <input type="text" id="flagInput" placeholder="플래그를 입력하세요">
        <button onclick="submitFlag()">제출</button>
        <p id="result"></p>
    </div>

    <div class="ssh-info">
        <h3 style="color: #00ff00;">SSH 정보</h3>
        <p>호스트: <span style="color: #00ff00;">192.168.1.54</span></p>
        <p>사용자: <span style="color: #00ff00;">flame20</span></p>
        <p>비밀번호: <span style="color: #00ff00;">1234</span></p>
        <p>포트: <span style="color: #00ff00;">2226</span></p>
    </div>


    <script>
        async function submitFlag() {
            const flag = document.getElementById('flagInput').value.trim();
            const result = document.getElementById('result');

            try {
                const response = await fetch('question20.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `flag=${encodeURIComponent(flag)}`
                });

                const data = await response.json();

                if (data.success) {
                    result.style.color = "#00ff00";
                    result.textContent = "정답입니다!";
                } else {
                    result.style.color = "#ff0000";
                    result.textContent = data.message;
                }
            } catch (error) {
                result.style.color = "#ff0000";
                result.textContent = "오류 발생: 서버에 연결할 수 없습니다.";
            }
        }
    </script>
</body>
</html>
