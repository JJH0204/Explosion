<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ACL 정책 요구사항
    $requiredLastLine = "permit ip any any";
    $permitRules = [
        "permit icmp host 192.168.30.120 host 192.168.10.110 echo",
        "permit icmp host 192.168.30.120 host 192.168.10.110 echo-reply",
        "permit tcp host 192.168.30.120 host 192.168.10.110 eq www",
        "permit tcp host 192.168.30.110 host 192.168.10.110 eq ftp"
    ];
    $denyRules = [
        "deny icmp host 192.168.30.100 host 192.168.10.100 echo",
        "deny icmp host 192.168.30.100 host 192.168.10.100 echo-reply",
        "deny icmp 192.168.30.0 0.0.0.255 host 192.168.10.110 echo",
        "deny icmp 192.168.30.0 0.0.0.255 host 192.168.10.110 echo-reply",
        "deny tcp 192.168.30.0 0.0.0.255 host 192.168.10.110 eq www"
    ];

    // POST 데이터 가져오기
    $data = json_decode(file_get_contents('php://input'), true);
    $userACL = trim($data['acl'] ?? '');

    // ACL 규칙을 줄 단위로 분리
    $userLines = explode("\n", $userACL);
    $userLines = array_map('trim', $userLines);

    // 입력값 검증
    if (empty($userLines) || count($userLines) < count($permitRules) + count($denyRules) + 1) {
        echo "ACL 규칙이 부족합니다.";
        exit;
    }

    // 마지막 줄 검증
    if ($userLines[count($userLines) - 1] !== $requiredLastLine) {
        echo "마지막 줄이 올바르지 않습니다.";
        exit;
    }

    // 허용 규칙 검증
    $permitFound = [];
    $denyFound = [];
    $remainingPermitRules = $permitRules;
    $remainingDenyRules = $denyRules;
    $denyEncountered = false;

    foreach ($userLines as $index => $line) {
        if ($line === $requiredLastLine) {
            continue; // 마지막 줄은 이미 검증됨
        }

        if (strpos($line, "permit") === 0) {
            if ($denyEncountered) {
                echo "permit 규칙이 deny 규칙 이후에 위치할 수 없습니다.";
                exit;
            }

            if (($key = array_search($line, $remainingPermitRules)) !== false) {
                $permitFound[] = $line;
                unset($remainingPermitRules[$key]);
            } else {
                echo "허용 규칙이 올바르지 않습니다.";
                exit;
            }
        } elseif (strpos($line, "deny") === 0) {
            $denyEncountered = true;

            if (($key = array_search($line, $remainingDenyRules)) !== false) {
                $denyFound[] = $line;
                unset($remainingDenyRules[$key]);
            } else {
                echo "차단 규칙이 올바르지 않습니다.";
                exit;
            }
        } else {
            echo "잘못된 ACL 명령어가 포함되어 있습니다.";
            exit;
        }
    }

    // 모든 허용 및 차단 규칙이 포함되었는지 확인
    if (!empty($remainingPermitRules) || !empty($remainingDenyRules)) {
        echo "모든 규칙이 올바르게 포함되지 않았습니다.";
        exit;
    }

    // 모든 검증이 통과된 경우 플래그 반환
    echo "정답입니다! ACL 정책이 올바르게 구성되었습니다.\n플래그: flag{ACL_RULES_CORRECT}";
    exit; // Stop further execution after handling the POST request
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 37: ACL Configuration</title>
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
            max-width: 700px;
            background-color: #1e1e1e;
            border: 1px solid #00ff00;
            border-radius: 5px;
        }

        textarea {
            background-color: #2a2a2a;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px;
            width: calc(100% - 20px);
            height: 250px;
            margin-bottom: 10px;
            box-sizing: border-box;
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

        #result, #flag {
            margin-top: 20px;
            font-size: 1.2em;
        }

        .instruction {
            background-color: #2a2a2a;
            color: #00ff00;
            padding: 15px;
            margin: 20px 0 30px 0;
            border-radius: 5px;
            text-align: left;
        }

        img {
            max-width: 100%;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>ACL Configuration Challenge</h1>
    <div class="container">
        <p>아래 이미지를 참고하여 ACL 정책을 설정하세요.</p>
        <img src="ACL.PNG" alt="ACL Diagram">
        <div class="instruction">
            <h3>정책 요구사항:</h3>
            <ul>
                <li>192.168.30.0 - > Server(192.168.10.110) : Ping, HTTP 접속 차단</li>
                <li>192.168.30.120 -> Server(192.168.10.110) : Ping, HTTP 허용</li>
                <li>192.168.30.100 -> 192.168.10.100 : Ping 차단</li>
                <li>192.168.30.110 -> Server(192.168.10.110) : FTP허용</li>
                <li>그 외 모든 트래픽은 허용</li>
            </ul>
        </div>

        <textarea id="aclCode" placeholder="여기에 ACL 설정 명령어를 입력하세요..."></textarea>
        <button onclick="checkACL()">제출</button>
        <p id="result"></p>
        <p id="flag"></p>
    </div>

    <script>
        async function checkACL() {
            const aclCode = document.getElementById('aclCode').value.trim();
            const response = await fetch("question37.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ acl: aclCode })
            });

            const resultText = await response.text(); // Change to text response
            const resultElement = document.getElementById('result');
            const flagElement = document.getElementById('flag');

            if (resultText.includes("정답입니다!")) {
                resultElement.style.color = '#00ff00';
                resultElement.textContent = resultText; // Display success message
                flagElement.style.color = '#00ff00';
                flagElement.textContent = ''; // Clear flag since it's now in result
            } else {
                resultElement.style.color = '#ff0000';
                resultElement.textContent = resultText; // Display error message
                flagElement.textContent = '';
            }
        }
    </script>
</body>
</html>