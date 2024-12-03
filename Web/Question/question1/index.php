<?php
// POST 요청 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON 데이터 받기
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);
    $userInput = $data['input'] ?? '';

    // 입력값 검증
    if ($userInput === 'admin') {
        // 추가된 보안 로그
        error_log("Admin access attempted by user.");
        echo json_encode([
            'success' => true,
            'flag' => 'ZmxhbWUxYW5zd2Vy'
        ]);
    } else {
        // 추가된 보안 로그
        error_log("Unauthorized access attempt with input: " . $userInput);
        echo json_encode([
            'success' => false
        ]);
    }
    exit; // PHP 로직 종료
}
?> 

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 1: Basic Wargame</title>
    <style>
        body {
            background-color: #1a1a1a;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #1e1e1e;
            padding: 2rem;
            border: 1px solid #00ff00;
            border-radius: 5px;
            text-align: center;
        }
        input {
            background-color: #2a2a2a;
            border: 1px solid #00ff00;
            color: #00ff00;
            padding: 5px 10px;
            margin: 10px;
            outline: none;
            border-radius: 3px;
        }
        button {
            background-color: #00ff00;
            color: #000000;
            border: none;
            padding: 5px 15px;
            cursor: pointer;
            border-radius: 3px;
            font-weight: bold;
        }
        button:hover {
            background-color: #00cc00;
        }
        #result {
            margin-top: 20px;
            font-weight: bold;
        }
        .error {
            color: #ff0000;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Basic Wargame</h2>
        <form id="adminForm">
            <label for="inputString">관리자 ID를 입력하세요: </label><br>
            <input type="text" id="inputString" name="inputString" required><br>
            <button type="button" onclick="checkAdmin()">로그인</button>
        </form>
        <p id="result"></p>
    </div>

    <script>
        function checkAdmin() {
            const input = document.getElementById("inputString").value;
            const result = document.getElementById("result");

            // AJAX를 사용하여 PHP로 데이터 전송
            fetch('question1.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ input: input })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    result.className = "";
                    result.textContent = "축하합니다! 플래그를 찾았습니다: " + data.flag;
                } else {
                    result.className = "error";
                    result.textContent = "접근 권한이 없습니다.";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                result.className = "error";
                result.textContent = "오류가 발생했습니다.";
            });
        }
    </script>
</body>

</html>