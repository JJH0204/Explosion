<?php
session_start();

// 플래그 설정
$flag = "FLAG{snake_master}";

// 점수 초기화
if (!isset($_SESSION['score'])) {
    $_SESSION['score'] = 0;
}

// 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 점수 파라미터 확인
    $score = isset($_GET['score']) ? intval($_GET['score']) : $_SESSION['score'];

    // 점수가 10점 이상인 경우에만 플래그 반환
    if ($score >= 10) {
        echo json_encode([
            'status' => 'success',
            'flag' => $flag
        ], JSON_UNESCAPED_UNICODE);
        exit;
    } else {
        $message = '게임 점수가 부족합니다.';
    }
} else {
    http_response_code(405);
    $message = '잘못된 요청 방식입니다.';
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge 17: Snake Game</title>
    <style>
        body {
            background-color: #121212;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            text-align: center;
            padding: 20px;
            margin: 0;
            min-height: 80vh;
        }

        .container {
            margin: 40px auto 0;
            padding: 20px;
            max-width: 600px;
            background-color: #1e1e1e;
            border: 1px solid #00ff00;
            border-radius: 5px;
        }

        canvas {
            display: block;
            margin: 20px auto;
            background-color: #2a2a2a;
            border: 1px solid #00ff00;
        }

        #score {
            font-size: 1.5rem;
            margin: 20px 0;
        }

        #result {
            width: 400px;
            margin: 20px auto;
            font-size: 1.2em;
            padding: 15px;
            box-sizing: border-box;
        }

        h1 {
            color: #00ff00;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Snake Game</h1>
        <p id="score">점수: <?php echo $_SESSION['score']; ?></p>
        <canvas id="gameCanvas" width="400" height="400"></canvas>
        <div id="result"><?php if (isset($message)) echo $message; ?></div>
    </div>

    <script>
        const canvas = document.getElementById("gameCanvas");
        const ctx = canvas.getContext("2d");

        const box = 20; // 한 칸의 크기
        const canvasSize = canvas.width / box; // 캔버스는 box 단위로 나뉨

        let snake = [{ x: 10, y: 10 }]; // 지렁이 초기 위치 (중앙)
        let direction = "RIGHT"; // 초기 방향
        let food = {
            x: Math.floor(Math.random() * canvasSize),
            y: Math.floor(Math.random() * canvasSize),
        };
        let score = 0;
        let gameSpeed = 200; // 게임 속도 (ms)
        let gameInterval;

        // 음식 그리기
        function drawFood() {
            ctx.fillStyle = "red";
            ctx.fillRect(food.x * box, food.y * box, box, box);
        }

        // 지렁이 그리기
        function drawSnake() {
            ctx.fillStyle = "#00ff00";
            snake.forEach((segment) => {
                ctx.fillRect(segment.x * box, segment.y * box, box, box);
            });
        }

        // 게임 오버 확인
        function checkCollision() {
            const head = snake[0];
            if (
                head.x < 0 ||
                head.y < 0 ||
                head.x >= canvasSize ||
                head.y >= canvasSize
            ) {
                return true;
            }

            for (let i = 1; i < snake.length; i++) {
                if (head.x === snake[i].x && head.y === snake[i].y) {
                    return true;
                }
            }
            return false;
        }

        // 서버에서 플래그 가져오기
        async function getFlag() {
            try {
                const response = await fetch(`./question17.php?score=${score}`);
                const data = await response.json();
                const resultElement = document.getElementById('result');

                if (data.status === 'success') {
                    resultElement.style.color = '#00ff00';
                    resultElement.innerHTML = `축하합니다! 플래그를 획득했습니다.<br>플래그: ${data.flag}`;
                } else {
                    resultElement.style.color = '#ff0000';
                    resultElement.textContent = data.message || '플래그를 가져오지 못했습니다. 다시 시도해주세요.';
                }
            } catch (error) {
                const resultElement = document.getElementById('result');
                resultElement.style.color = '#ff0000';
                resultElement.textContent = '플래그 요청 중 오류가 발생했습니다.';
            }
        }

        // 게임 업데이트
        async function updateGame() {
            const head = { ...snake[0] };

            if (direction === "UP") head.y -= 1;
            if (direction === "DOWN") head.y += 1;
            if (direction === "LEFT") head.x -= 1;
            if (direction === "RIGHT") head.x += 1;

            snake.unshift(head);

            if (head.x === food.x && head.y === food.y) {
                score++;
                document.getElementById("score").textContent = `점수: ${score}`;
                food = {
                    x: Math.floor(Math.random() * canvasSize),
                    y: Math.floor(Math.random() * canvasSize),
                };

                if (score === 10) {
                    await getFlag(); // 서버에서 플래그 가져오기
                }
            } else {
                snake.pop();
            }

            if (checkCollision()) {
                clearInterval(gameInterval);
                const resultElement = document.getElementById('result');
                if (!resultElement.textContent.includes('플래그')) {
                    resultElement.style.color = '#ff0000';
                    resultElement.textContent = `게임 오버! 최종 점수: ${score}`;
                }
            }
        }

        // 방향 업데이트
        document.addEventListener("keydown", (e) => {
            if (e.key === "ArrowUp" && direction !== "DOWN") direction = "UP";
            if (e.key === "ArrowDown" && direction !== "UP") direction = "DOWN";
            if (e.key === "ArrowLeft" && direction !== "RIGHT") direction = "LEFT";
            if (e.key === "ArrowRight" && direction !== "LEFT") direction = "RIGHT";
        });

        // 게임 루프
        function gameLoop() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            drawFood();
            drawSnake();
            updateGame();
        }

        // 게임 시작
        function startGame() {
            gameInterval = setInterval(gameLoop, gameSpeed);
        }

        startGame();
    </script>
</body>
</html>

