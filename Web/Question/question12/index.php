<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// 정답 전화번호 설정
$correct_number = "07082729218";  // 하이픈 제외
$flag = "FLAG{C4ll_M3_M4yB3_B4by}";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_number = isset($_POST['number']) ? $_POST['number'] : '';
    
    // 입력값에서 하이픈 제거
    $user_number = str_replace('-', '', $user_number);
    
    if ($user_number === $correct_number) {
        echo json_encode([
            'success' => true,
            'flag' => $flag
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => '틀린 전화번호입니다.'
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
    <title>Challenge 12: Call Correct Phone Number</title>
    <style>
        body {
            background-color: #121212;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            text-align: center;
            padding: 20px;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .container {
            margin: 0 auto;
            padding: 20px;
            max-width: 600px;
            width: 100%;
            box-sizing: border-box;
            background-color: #1e1e1e;
            border: 1px solid #00ff00;
            border-radius: 5px;
        }

        .phone-display {
            font-size: 2em;
            margin: 20px 0;
            padding: 15px;
            background-color: #2a2a2a;
            border: 1px solid #00ff00;
            border-radius: 5px;
            font-family: 'Digital', 'Courier New', monospace;
            letter-spacing: 2px;
        }

        .dial-container {
            background-color: #2a2a2a;
            border: 1px solid #00ff00;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
        }

        .dial-slider {
            -webkit-appearance: none;
            width: 100%;
            height: 12px;
            border-radius: 5px;
            background: #1e1e1e;
            outline: none;
            border: 1px solid #00ff00;
            margin: 30px 0;
            cursor: col-resize;
        }

        .dial-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 40px;
            border-radius: 4px;
            background: #00ff00;
            cursor: col-resize;
            transition: all 0.2s;
        }

        .dial-slider::-webkit-slider-thumb:hover {
            background: #33ff99;
            box-shadow: 0 0 10px #00ff00;
        }

        .dial-slider::-moz-range-thumb {
            width: 20px;
            height: 40px;
            border-radius: 4px;
            background: #00ff00;
            cursor: col-resize;
            transition: all 0.2s;
            border: none;
        }

        .dial-slider::-moz-range-thumb:hover {
            background: #33ff99;
            box-shadow: 0 0 10px #00ff00;
        }

        .selected-number {
            font-size: 1.5em;
            margin: 20px 0;
            color: #00ff7f;
        }

        button {
            background-color: #00ff00;
            color: #000;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin: 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
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
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            background-color: #2a2a2a;
            border-radius: 5px;
            border: 1px solid #00ff00;
        }

        .hint h3 {
            margin-top: 0;
            margin-bottom: 15px;
        }

        .hint p {
            margin: 0;
        }

        .controls {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <h1>Call Correct Phone Number</h1>
    <div class="container">
        <p>화면에 표시된 전화번호를 정확하게 입력하세요!</p>
        <div class="phone-display" id="targetNumber">070-8272-9218</div>
        
        <div class="dial-container">
            <div class="selected-number" id="selectedNumber">선택된 번호: </div>
            <input type="range" 
                   id="phoneSlider" 
                   class="dial-slider" 
                   min="0" 
                   max="10000000000" 
                   value="0" 
                   step="1">
            <div class="controls">
                <button class="control-button" onclick="clearNumber()">지우기</button>
                <button class="control-button" onclick="checkNumber()">전화 걸기</button>
            </div>
        </div>

        <p id="result"></p>
    </div>

    <div class="hint">
        <h3>힌트</h3>
        <p>슬라이더를 움직여 올바른 전화번호를 맞추면 플래그를 획득할 수 있습니다.</p>
    </div>

    <script>
        const slider = document.getElementById('phoneSlider');
        const selectedNumberDisplay = document.getElementById('selectedNumber');

        // URL에서 공유된 번호 파라미터 확인
            function getSharedNumber() {
                const urlParams = new URLSearchParams(window.location.search);
                const shared = urlParams.get('shared');
                if (shared) {
                    // XSS 취약점: shared 파라미터 값을 직접 innerHTML로 삽입
                    document.getElementById('selectedNumber').innerHTML = '선택된 번호: ' + shared;
                    // 슬라이더 값도 업데이트
                    if (!isNaN(shared.replace(/-/g, ''))) {
                        slider.value = shared.replace(/-/g, '');
                    }
                }
            }

        function updateDisplay(value) {
            let displayNumber = String(value).padStart(11, '0');
            displayNumber = displayNumber.slice(0, 3) + '-' + 
                          displayNumber.slice(3, 7) + '-' + 
                          displayNumber.slice(7);
            selectedNumberDisplay.textContent = '선택된 번호: ' + displayNumber;
        }

        slider.oninput = function() {
            updateDisplay(this.value);
        }

        function clearNumber() {
            slider.value = 0;
            updateDisplay(0);
        }

        function checkNumber() {
            const userNumber = String(slider.value).padStart(11, '0');
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'question12.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                const resultElement = document.getElementById('result');
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        resultElement.style.color = '#00ff7f';
                        resultElement.textContent = `정답입니다! 플래그: ${response.flag}`;
                    } else {
                        resultElement.style.color = '#ff0000';
                        resultElement.textContent = '틀렸습니다. 다시 시도해보세요.';
                    }
                }
            };
            
            xhr.send('number=' + encodeURIComponent(userNumber));
        }

        // 페이지 로드 시 실행
        document.addEventListener('DOMContentLoaded', function() {
            updateDisplay(0);
            getSharedNumber();  // URL 파라미터 확인

            // 슬라이더의 방향키 조작 방지
            slider.addEventListener('keydown', function(e) {
                if(e.key === 'ArrowLeft' || e.key === 'ArrowRight' || 
                   e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>

