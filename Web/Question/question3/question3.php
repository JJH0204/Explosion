<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    $userInput = $data['input'] ?? '';

    // 정답과 플래그를 서버 측 상수로 정의
    define('CORRECT_ANSWER', 'aGFja2VyMTIz');
    define('FLAG', 'FLAG{base64_decode_success}');

    try {
        if ($userInput === CORRECT_ANSWER) {
            echo json_encode([
                'success' => true,
                'message' => '정답입니다! 플래그는: ' . FLAG
            ]);
        } else {
            echo json_encode([
                'success' => false
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => '에러 발생'
        ]);
    } exit;
}
?> 

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 3: 404 Not Found</title>
    <style>
        body {
            background-color: #121212;
            color: #00ff00;
            font-family: monospace;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background-color: #1e1e1e;;
            padding: 2rem;
            border-radius: 10px;
            border: 1px solid #00ff00;
        }
        #login-form {
            display: none;
            margin-top: 20px;
        }
        .error-msg {
            color: red;
            font-size: 12px;
        }
        input {
            background-color: #2a2a2a;
            border: 1px solid #00ff00;
            color: #00ff00;
            padding: 5px 10px;
            margin: 10px;
            border-radius: 3px;
        }
        button {
            background-color: #00ff00;
            color: #000;
            border: none;
            padding: 5px 15px;
            cursor: pointer;
            border-radius: 3px;
            font-weight: bold;
        }
        #result {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <!-- 힌트1: 소스 코드를 확인하세요 -->
    <div class="container">
        <h1>Page Not Found</h1>
        <div id="login-form">
            <input type="password" id="auth-key" placeholder="인증 키를 입력하세요">
            <button onclick="authenticate()">확인</button>
            <p id="result"></p>
        </div>
    </div>

    <script>
        const _0x4e8a=['getElementById','value','auth-key','result','innerHTML','display','block','login-form','에러 발생:','&amp;','&lt;','&gt;','&quot;','&#039;','YUdGamEyVnlNVEl6'];
        const _0x2d4f=function(_0x4e8aa){return _0x4e8a[_0x4e8aa]};
        console.log('힌트2: 비밀 키는 Base64로 인코딩되어 있습니다.');
        console.log('힌트3: 개발자 도구의 콘솔에서 atob() 함수를 사용해보세요.');

        function authenticate() {
            const input = document.getElementById('auth-key').value;
            const resultElement = document.getElementById('result');
            
            fetch('question3.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ input: input })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    resultElement.style.color = '#00ff00';
                    resultElement.textContent = data.message;
                } else {
                    resultElement.style.color = '#ff0000';
                    resultElement.textContent = '틀렸습니다!';
                }
            })
            .catch(error => {
                console.log('에러 발생:', error);
            });
        }

        window.onload = function() {
            document.getElementById('login-form').style.display = 'block';
        };
    </script>
</body>
</html>
