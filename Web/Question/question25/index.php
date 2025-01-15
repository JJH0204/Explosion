<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['nickname']) || !isset($_POST['flag'])) {
        die(json_encode(['error' => '잘못된 접근입니다.']));
    }

    $host = 'localhost';
    $user = 'db_admin';
    $password = 'flamerootpassword';
    $database = 'userDB';

    // db_admin 계정으로 데이터베이스 연결
    $conn = new mysqli($host, $user, $password, $database);

    if ($conn->connect_error) {
        die(json_encode(['error' => '데이터베이스 연결 실패: ' . $conn->connect_error]));
    }

    // 사용자의 닉네임과 입력한 플래그 가져오기
    $nickname = $conn->real_escape_string($_SESSION['nickname']);
    $inputFlag = $conn->real_escape_string($_POST['flag']);

    // 해당 사용자의 테이블에서 플래그 확인
    $query = "SELECT flag FROM `$nickname` WHERE flag = '$inputFlag'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => '잘못된 플래그입니다.']);
    }

    $conn->close();
    exit; // Ensure no further output is sent
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 25: Personal Flag</title>
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

        textarea {
            background-color: #2a2a2a;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px;
            width: 80%;
            margin-bottom: 10px;
            resize: none;
        }

        button {
            background-color: #00ff00;
            color: #000;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
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
        background-color: #1e1e1e;
        border: 1px solid #00ff00;
        border-radius: 5px;
        max-width: 600px;
    }

    .putty-info {
        margin-top: 20px;
        text-align: left;
        padding: 15px;
        background-color: #2a2a2a;
        border-radius: 5px;
    }

    .putty-info ol {
        margin-left: 20px;
        padding-left: 20px;
    }

    .putty-info li {
        margin: 5px 0;
    }

    button {
        background-color: #00ff00;
        color: #000;
        border: none;
        padding: 10px 20px;
        margin: 10px 0;
        cursor: pointer;
        border-radius: 5px;
        font-weight: bold;
    }

    button:hover {
        background-color: #33ff99;
    }

    .command {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 10px 0;
    }

    code {
        background-color: #2a2a2a;
        padding: 5px 10px;
        border-radius: 3px;
    }

    .sql-terminal {
        margin-top: 3px;
        text-align: left;
    }
    
    .sql-terminal textarea {
        width: 96%;
        margin-bottom: 10px;
        font-family: 'Courier New', monospace;
        background-color: #2a2a2a;
        color: #00ff00;
    }
    
    #queryResult {
        margin-top: 15px;
        padding: 10px;
        background-color: #2a2a2a;
        border: 1px solid #00ff00;
        min-height: 100px;
        max-height: 300px;
        overflow-y: auto;
    }
    </style>
</head>
<body>
    <h1>Personal Flag</h1>
    <div class="container">
    <p id="result"></p>

    <div class="sql-terminal">
        <h2>SQL 터미널</h2>
        <textarea id="sqlQuery" placeholder="SQL 쿼리를 입력하세요..." rows="4"></textarea>
        <button onclick="executeQuery()">쿼리 실행</button>
        <div id="queryResult"></div>
    </div>
    </div>


    </div>

    <script>
    function executeQuery() {
        const query = document.getElementById('sqlQuery').value;
        const resultDiv = document.getElementById('queryResult');
        
        fetch('execute_query.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'query=' + encodeURIComponent(query)
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                resultDiv.innerHTML = `<span style="color: #ff4d4d;">오류: ${data.error}</span>`;
            } else if (data.success) {
                resultDiv.innerHTML = `<span style="color: #00ff7f;">${data.success}</span>`;
            } else if (data.data) {
                let output = '<table border="1" style="width:100%; border-collapse: collapse;">';
                // 테이블 헤더 생성
                if (data.data.length > 0) {
                    output += '<tr>';
                    for (let key in data.data[0]) {
                        output += `<th style="padding: 5px;">${key}</th>`;
                    }
                    output += '</tr>';
                }
                // 데이터 행 생성
                data.data.forEach(row => {
                    output += '<tr>';
                    for (let key in row) {
                        output += `<td style="padding: 5px;">${row[key]}</td>`;
                    }
                    output += '</tr>';
                });
                output += '</table>';
                resultDiv.innerHTML = output;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `<span style="color: #ff4d4d;">오류: ${error.message}</span>`;
        });
    }

    </script>
</body>
</html>