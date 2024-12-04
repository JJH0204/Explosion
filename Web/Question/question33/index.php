<?php
// 정답 ACL (순서를 강제하는 첫 번째와 마지막 줄)
$requiredFirstLine = "permit tcp 150.150.128.0 0.0.31.255 host 100.100.100.20 eq www";
$requiredLastLine = "permit ip any any";

// 나머지 정답 ACL (순서는 상관없음)
$correctRules = [
    "deny tcp 150.150.96.0 0.0.31.255 host 100.100.100.20 eq www",
    "deny tcp 150.150.128.0 0.0.31.255 host 100.100.100.20 eq www",
    "deny tcp 150.150.160.0 0.0.31.255 host 100.100.100.20 eq www",
    "deny icmp 150.150.96.0 0.0.31.255 host 100.100.100.20 echo",
    "deny icmp 150.150.96.0 0.0.31.255 host 100.100.100.20 echo-reply",
    "deny icmp 150.150.128.0 0.0.31.255 host 100.100.100.20 echo",
    "deny icmp 150.150.128.0 0.0.31.255 host 100.100.100.20 echo-reply",
    "deny icmp 150.150.160.0 0.0.31.255 host 100.100.100.20 echo",
    "deny icmp 150.150.160.0 0.0.31.255 host 100.100.100.20 echo-reply"
];

// POST 데이터 가져오기
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userACL = trim($data['acl'] ?? '');

    // 입력값을 줄 단위로 분리
    $userLines = explode("\n", $userACL);
    $userLines = array_map('trim', $userLines); // 공백 제거

    // 입력값 검증
    if (empty($userLines) || count($userLines) < 2) {
        echo json_encode(["status" => "error", "message" => "ACL 규칙을 입력하세요."]);
        exit;
    }

    // 첫 번째 줄 검증
    if ($userLines[0] !== $requiredFirstLine) {
        echo json_encode(["status" => "error", "message" => "첫 번째 줄이 올바르지 않습니다."]);
        exit;
    }

    // 마지막 줄 검증
    if ($userLines[count($userLines) - 1] !== $requiredLastLine) {
        echo json_encode(["status" => "error", "message" => "마지막 줄이 올바르지 않습니다."]);
        exit;
    }

    // 중간 줄 검증 (순서 상관없음)
    $middleLines = array_slice($userLines, 1, -1); // 첫 번째와 마지막 줄 제거
    $remainingRules = $correctRules;

    foreach ($middleLines as $line) {
        if (($key = array_search($line, $remainingRules)) !== false) {
            unset($remainingRules[$key]); // 매칭된 규칙 제거
        } else {
            echo json_encode(["status" => "error", "message" => "중간 줄에 올바르지 않은 규칙이 포함되어 있습니다."]);
            exit;
        }
    }

    // 모든 검증이 통과된 경우 플래그 반환
    if (empty($remainingRules)) {
        echo json_encode(["status" => "success", "flag" => "FLAG{ACL_RULES_CORRECT}"]);
    } else {
        echo json_encode(["status" => "error", "message" => "중간 줄 규칙이 부족합니다."]);
    }
    exit; 
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 33: ACL Configuration</title>
    <style>
        body {
            background-color: #121212;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            text-align: center;
            padding: 20px;
            margin: 0;
            min-height: 100vh;
        }

        .container {
            margin: 40px auto 0;
            padding: 20px;
            max-width: 800px;
            background-color: #1e1e1e;
            border: 1px solid #00ff00;
            border-radius: 5px;
        }

        textarea {
            background-color: #2a2a2a;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px;
            width: 95%;
            height: 250px;
            margin: 10px auto;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }

        button {
            background-color: #00ff00;
            color: #000;
            border: 1px solid #00ff00;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
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
            padding: 20px;
            margin: 20px auto;
            border-radius: 5px;
            text-align: left;
            border: 1px solid #00ff00;
        }

        img {
            max-width: 100%;
            margin: 20px 0;
            border: 1px solid #00ff00;
            border-radius: 5px;
        }

        .instruction ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .instruction li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>ACL Configuration</h1>
    <div class="container">
        <p>아래 이미지를 참고하여 ACL 정책을 설정하세요.</p>
        <img src="ACL.PNG" alt="ACL Diagram">
        <div class="instruction">
            <h3>정책 요구사항:</h3>
            <ul>
                <li>VLAN -> WEB : Ping, HTTP 모두 차단</li>
                <li>V20 -> WEB : HTTP만 허용</li>
                <li>그 외 모든 트래픽 허용</li>
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
            const response = await fetch("question33.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ acl: aclCode })
            });

            const result = await response.json();
            const resultElement = document.getElementById('result');
            const flagElement = document.getElementById('flag');

            if (result.status === "success") {
                resultElement.style.color = '#00ff00';
                resultElement.textContent = "정답입니다! ACL 정책이 올바르게 구성되었습니다.";
                flagElement.style.color = '#00ff00';
                flagElement.textContent = `플래그: ${result.flag}`;
            } else {
                resultElement.style.color = '#ff0000';
                resultElement.textContent = "틀렸습니다. ACL 정책을 다시 확인하세요.";
                flagElement.textContent = "";
            }
        }
    </script>
</body>
</html>
