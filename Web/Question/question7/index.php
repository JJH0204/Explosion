<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // POST 데이터 받기
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    // 토큰 확인
    $success_token = $data['token'] ?? '';

    if ($success_token !== 'valid_game_completion') {
        echo json_encode([
            'success' => false,
            'message' => '유효하지 않은 토큰입니다.'
        ]);
        exit;
    }

    $flag = "FLAG{find_the_O_success}";
    echo json_encode([
        'success' => true,
        'message' => '게임 완료!',
        'flag' => $flag
    ]);
    exit;
}
?> 

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 7: O Finder Game</title>
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

        .character-grid {
            display: grid;
            grid-template-columns: repeat(20, 25px);
            gap: 3px;
            justify-content: center;
            margin: 20px auto;
            padding: 15px;
            background-color: #2a2a2a;
            border-radius: 5px;
            border: 1px solid #00ff00;
        }

        .character {
            background-color: #1e1e1e;
            color: #00ff00;
            border: 1px solid #00ff00;
            width: 25px;
            height: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            border-radius: 3px;
            font-size: 0.9em;
        }

        .correct {
            background-color: #00ff7f; /* Correct answer highlight */
        }

        .wrong {
            background-color: #ff4d4d; /* Wrong answer highlight */
        }

        .hint {
            background-color: #ffff00; /* Hint highlight */
        }

        button {
            background-color: #00ff00;
            color: #000;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            margin-top: 20px;
            font-weight: bold;
            border-radius: 5px;
        }

        button:hover {
            background-color: #33ff99;
        }

        #timer {
            background-color: #2a2a2a;
            padding: 10px;
            margin: 20px 0;
            border-radius: 5px;
            border: 1px solid #00ff00;
        }
    </style>
</head>
<body>
    <h1>O Finder Game</h1>
    <div class="container">
        <p>아래 0들 사이에서 <b>O</b>를 찾으세요!</p>
        <div id="timer">남은 시간: <span id="timeLeft">20</span>초</div>
        <div class="character-grid" id="characterGrid"></div>
        <button onclick="startGame()">게임 시작</button>
        <p id="result"></p>
    </div>

    <script>
        const gridContainer = document.getElementById('characterGrid');
        const resultElement = document.getElementById('result');
        const timerElement = document.getElementById('timeLeft');

        let timer;
        let timeRemaining = 20;
        let correctIndex = -1;
        let isGameActive = false;

        function startGame() {
            gridContainer.innerHTML = '';
            resultElement.textContent = '';
            resultElement.style.color = '#00ff00';
            timeRemaining = 20;
            timerElement.textContent = timeRemaining;
            isGameActive = true;

            const characters = generateCharacters();
            characters.forEach((char, index) => {
                const charElement = document.createElement('div');
                charElement.textContent = char;
                charElement.className = 'character';
                charElement.onclick = () => checkCharacter(char, index);
                gridContainer.appendChild(charElement);
            });

            startTimer();
        }

        function generateCharacters() {
            const characters = [];
            const gridSize = 400;
            correctIndex = Math.floor(Math.random() * gridSize);

            for (let i = 0; i < gridSize; i++) {
                characters.push(i === correctIndex ? 'O' : '0');
            }

            return characters;
        }

        function checkCharacter(selectedChar, index) {
            if (!isGameActive) return;

            isGameActive = false;

            if (selectedChar === 'O') {
                fetch('question7.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        token: 'valid_game_completion'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.flag) {
                        resultElement.style.color = '#00ff7f';
                        resultElement.textContent = `정답입니다! 플래그: ${data.flag}`;
                    } else {
                        resultElement.style.color = '#ff4d4d';
                        resultElement.textContent = '오류가 발생했습니다.';
                    }
                });
            } else {
                resultElement.style.color = '#ff4d4d';
                resultElement.textContent = '틀렸습니다. 새로고침하여 다시 시작하세요.';
                highlightCorrect(correctIndex, false);
                lockGame();
            }

            clearTimeout(timer);
        }

        function highlightCorrect(index, isCorrect) {
            const cells = document.querySelectorAll('.character');
            cells[index].classList.add(isCorrect ? 'correct' : 'wrong');

            if (!isCorrect) {
                cells[correctIndex].classList.add('correct');
            }
        }

        function startTimer() {
            timer = setInterval(() => {
                timeRemaining--;
                timerElement.textContent = timeRemaining;

                if (timeRemaining <= 0) {
                    clearTimeout(timer);
                    if (isGameActive) {
                        isGameActive = false;
                        resultElement.style.color = '#ff4d4d';
                        resultElement.textContent = '시간 초과! 새로고침하여 다시 시작하세요.';
                        highlightCorrect(correctIndex, false);
                        lockGame();
                    }
                }
            }, 1000);
        }

        function lockGame() {
            const cells = document.querySelectorAll('.character');
            cells.forEach(cell => {
                cell.onclick = null;
            });

            const startButton = document.querySelector('button');
            startButton.disabled = true;
            startButton.style.backgroundColor = '#555';
            startButton.style.cursor = 'not-allowed';
        }

        window.addEventListener('load', () => {
            //console.log('게임에 오신 것을 환영합니다! 정답 힌트를 원하면 "hint"를 입력하세요.');
            Object.defineProperty(window, 'hint', {
                get: function () {
                    if (isGameActive) {
                        const cells = document.querySelectorAll('.character');
                        cells[correctIndex].classList.add('hint');
                        console.log('힌트: 정답 위치가 노란색으로 표시되었습니다.');
                    } else {
                        console.log('힌트를 제공할 수 없습니다. 게임이 비활성 상태입니다.');
                    }
                    return '힌트를 제공했습니다.';
                }
            });
        });
        document.addEventListener('keydown', (event) => {
        // Ctrl+F 감지
        if (event.ctrlKey && event.key === 'f') {
            event.preventDefault(); // 기본 동작 막기
            alert('Ctrl+F 검색 기능이 비활성화되어 있습니다.');
        }

        // Cmd+F (MacOS) 감지
        if (event.metaKey && event.key === 'f') {
            event.preventDefault();
            alert('Cmd+F 검색 기능이 비활성화되어 있습니다.');
        }
    });
    </script>
</body>
</html>

