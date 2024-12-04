<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$input = json_decode(file_get_contents('php://input'), true);

$message = ''; // 메시지를 저장할 변수

if (isset($input['success']) && $input['success'] === true) {
    $message = '성공: FLAG{XxXx_h4rd_t0_gu3ss_XxXx_39th_Ch4ll3ng3}';
} else {
    $message = '오류: Invalid attempt';
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Challenge 39: Number Clicking</title>
    <link rel="stylesheet" href="style39.css">
</head>
<body>
    <div id="game-container">
        <div id="header">
            <h1>Number Clicking Challenge</h1>
            <div id="timer">남은 시간: <span id="time-left">30.000</span>초</div>
        </div>
        
        <div id="main-content">
            <div id="game-area">
                <div id="start-section">
                    <button id="start-button">게임 시작</button>
                    <div id="countdown"></div>
                </div>
                <div id="message"><?php echo $message; ?></div> <!-- 메시지 출력 -->
            </div>
            
            <div id="side-panel">
                <div id="best-time">
                    <h2>최고 기록</h2>
                    <span id="best-time-value">없음</span>
                </div>
                <div id="history-list">
                    <h2>기록 히스토리</h2>
                    <ul id="history-items"></ul>
                </div>
            </div>
        </div>

    </div>
    <script src="question39.js"></script>
</body>
</html>
