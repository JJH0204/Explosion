<?php
// POST 데이터 받기
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if ($data && isset($data['role'])) {
    if ($data['role'] === 'admin') {
        echo json_encode([
            'success' => true,
            'message' => '관리자로 로그인 성공!',
            'flag' => 'FLAG{proxy_role_manipulation_success}'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '일반 사용자로 로그인됨'
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
    <title>Challenge 4: Admin Portal</title>
    <style>
        body {
            background-color: #121212;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .container {
            background-color: #1e1e1e;
            padding: 30px;
            border: 1px solid #00ff00;
            border-radius: 5px;
            text-align: center;
            width: 300px;
            max-width: 90%;
            box-sizing: border-box;
        }
        h1 {
            margin-bottom: 20px;
        }
        input, button {
            background-color: #2e2e2e;
            color: #33ff33;
            border: 1px solid #00ff00;
            padding: 8px 15px;
            margin: 5px 0;
            width: 80%;
            border-radius: 3px;
        }
        button {
            cursor: pointer;
            margin-top: 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        button:hover {
            background-color: #00ff00;
            color: #0a0a0a;
        }
        .hidden {
            display: none;
        }
        #error-msg {
            color: #ff3333;
        }
        #result {
            margin-top: 20px;
            width: 100%;
            text-align: center;
        }
        .success, .error {
            margin: 10px auto;
            padding: 10px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .success {
            color: #00ff00;
        }
        .error {
            color: #ff3333;
        }
        .success p, .error p {
            margin: 5px 0;
            width: 100%;
            white-space: normal;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <div id="login-form">
            <form onsubmit="return handleLogin(event)">
                <input type="text" name="username" placeholder="사용자 이름"><br>
                <input type="password" name="password" placeholder="비밀번호"><br>
                <input type="hidden" name="role" value="user">
                <button type="submit">로그인</button>
            </form>
        </div>
        <div id="result"></div>
    </div>

    <script>
        function handleLogin(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const data = {
                username: formData.get('username'),
                password: formData.get('password'),
                role: formData.get('role')
            };

            fetch('question4.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                const resultDiv = document.getElementById('result');
                if(result.success) {
                    resultDiv.innerHTML = `
                        <div class="success">
                            <p>${result.message}</p>
                            <p>${result.flag}</p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="error">
                            <p>${result.message}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('result').innerHTML = `
                    <div class="error">
                        <p>오류가 발생했습니다.</p>
                    </div>
                `;
            });
            
            return false;
        }
    </script>
</body>
</html>
