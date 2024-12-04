<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $input = $data['input'] ?? '';

    // 정확히 32바이트 + "BOFB" 패턴 검증
    if (strlen($input) === 36 && substr($input, 32, 4) === "BOFB") {
        echo json_encode([
            'status' => 'success',
            'flag' => 'FLAG{advanced_buffer_overflow}'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => '버퍼 오버플로우 공격이 실패했습니다.'
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
    <title>Challenge 13: Buffer Overflow</title>
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
            margin-bottom: 10px;
            border-radius: 3px;
        }

        button {
            background-color: #00ff00;
            color: #000;
            border: none;
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

        .hint {
            margin-top: 20px;
            font-size: 0.9em;
            color: #33ff33;
        }
    </style>
</head>
<body>
    <h1>Buffer Overflow</h1>
    <div class="container">
        <p>적절한 값을 입력하여 숨겨진 결과를 찾아보세요.</p>
        <p class="hint">
            힌트: 입력 값은 <b>32자 이상</b>이어야 합니다.
        </p>
        <p class="hint" id="magicHint" style="display: none;"></p>
        <input type="text" id="inputBuffer" placeholder="값을 입력하세요" oninput="checkMagicNumber()">
        <button onclick="checkOverflow()">버퍼 전송</button>
        <p id="result"></p>
    </div>

    <script>
        // 16진수를 ASCII 문자열로 변환하는 함수
        function hexToAscii(hex) {
            let str = '';
            for (let i = 0; i < hex.length; i += 2) {
                str += String.fromCharCode(parseInt(hex.substr(i, 2), 16));
            }
            return str;
        }

        const magicNumber = "424F4642"; // 특정 조건 값
        
        // 입력값 확인 및 힌트 표시 함수
        function checkMagicNumber() {
            const input = document.getElementById('inputBuffer').value;
            const magicHintElement = document.getElementById('magicHint');
            
            if (input === magicNumber) {
                magicHintElement.textContent = `힌트: ${magicNumber} (ASCII: ${hexToAscii(magicNumber)})`;
                magicHintElement.style.display = 'block';
            } else {
                magicHintElement.style.display = 'none';
            }
        }
        
        // 입력 값을 서버로 전송
        async function checkOverflow() {
            const input = document.getElementById('inputBuffer').value;
            const result = document.getElementById('result');

            try {
                const response = await fetch('index.php', { // PHP로 요청
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ input })
                });

                const data = await response.json();

                if (data.status === "success") {
                    result.style.color = '#00ff00';
                    result.textContent = `축하합니다! 플래그: ${data.flag}`;
                } else {
                    result.style.color = '#ff0000';
                    result.textContent = data.message;
                }
            } catch (error) {
                result.style.color = '#ff0000';
                result.textContent = '오류 발생: 서버에 연결할 수 없습니다.';
            }
        }
    </script>
</body>
</html>
