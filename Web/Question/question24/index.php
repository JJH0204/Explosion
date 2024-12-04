<?php
session_start();

// CSRF 방지
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    serveHtml();
    exit;
}

// POST 요청 확인
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 제출된 플래그 가져오기
    $submitted_flag = $_POST['flag'] ?? '';
    $correct_flag = "flag{24Challengesflag}";

    if ($submitted_flag === $correct_flag) {
        echo json_encode([
            'success' => true,
            'flag' => "FLAG{UU24encode_challenge}"
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid flag'
        ]);
    }
    exit;
}

function serveHtml() {
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 24: Decoding</title>
    <link rel="stylesheet" href="style24.css">
    <style>
        #downloadSection, #flagForm {
            display: none;
        }
    </style>
</head>
<body>
    <h1>Decoding</h1>
    <div class="container">
        <p>File - Insecure storage.</p>
        <div class="download-section">
            <form id="loginForm" class="login-form" method="POST">
                <div id="loginMessage"></div>
                <div>
                    <input type="text" id="username" placeholder="사용자 이름">
                </div>
                <div>
                    <input type="password" id="password" placeholder="비밀번호">
                </div>
                <button type="button" onclick="login()">로그인</button>
            </form>
            <div id="downloadSection">
                <a href="./download/UU24encode.txt" download class="download-button">파일 다운로드</a>
            </div>
        </div>
        <p>해독된 사용자 패스워드를 입력하세요:</p>
        <form id="flagForm" method="POST" onsubmit="return checkFlag();">
            <input type="text" name="flag" id="userInput" placeholder="해독된 플래그 입력">
            <div class="button-group">
                <button type="submit">제출</button>
            </div>
        </form>
        <p id="result"></p>
    </div>

    <script src="question24.js"></script>
    <script>
        function checkFlag() {
            const form = document.getElementById('flagForm');
            const formData = new FormData(form);

            fetch('index.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const resultElement = document.getElementById('result');
                if (data.success) {
                    resultElement.textContent = data.flag; 
                } else {
                    resultElement.textContent = data.message; 
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });

            return false;
        }

        function login() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;

            // 로그인 정보 확인
            if (username === 'admin' && password === 'adminpass') {
                document.getElementById('downloadSection').style.display = 'block'; // 다운로드 섹션 표시
                document.getElementById('flagForm').style.display = 'block'; // 플래그 입력란 표시
            } else {
                document.getElementById('loginMessage').textContent = '로그인 실패: 잘못된 사용자 이름 또는 비밀번호';
            }
        }
    </script>
</body>
</html>
<?php
}
?>