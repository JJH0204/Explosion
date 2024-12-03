<?php
session_start();

// CSRF 방지
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'question29.php') === false) {
        http_response_code(403);
        die(json_encode(['error' => 'Access Denied']));
    }

    // 점수가 1000점 이상인 경우에만 flag 반환
    if (isset($_POST['score']) && intval($_POST['score']) >= 1000) {
        echo json_encode([
            'success' => true,
            'flag' => 'flag{4368616c6c656e6765666c6167}'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '1000점 이상 획득해야 합니다.'
        ]);
    }
    exit; // Ensure no further output is sent
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 29: Linux Typing Game</title>
    <link rel="stylesheet" href="style.css">
    <script src="question29.js" defer></script>
</head>
<body>
    <div id="game-container"></div>
    <div id="timer">시간: <span id="time-value">60</span>초</div>
    <div id="goal">목표: 1000점</div>
    <div id="score">점수: <span id="score-value">0</span></div>
    
    <div id="game-start">
        <h2>Linux Typing Game</h2>
        <button id="start-btn">게임 시작</button>
    </div>

    <div id="game-over" style="display: none;">
        <h2 id="result-message"></h2>
        <button id="restart-btn">다시 시작</button>
    </div>

    <div id="input-area">
        <input type="text" id="typing-input" placeholder="여기에 타이핑하세요." autocomplete="off" disabled style="text-align: center;">
    </div>

    <div id="result-div"></div>
</body>
</html>
