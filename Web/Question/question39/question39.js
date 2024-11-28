document.addEventListener('DOMContentLoaded', () => {
    const gameArea = document.getElementById('game-area');
    const timerElement = document.getElementById('time-left');
    const messageElement = document.getElementById('message');
    const startButton = document.getElementById('start-button');
    const startSection = document.getElementById('start-section');
    const countdownElement = document.getElementById('countdown');
    const timerSection = document.getElementById('timer');
    const historyItems = document.getElementById('history-items');
    const bestTimeValue = document.getElementById('best-time-value');
    
    let currentNumber = 50;
    let gameActive = false;
    let timer;
    let startTime;
    let bestTime = localStorage.getItem('bestTime') || null;
    let gameHistory = JSON.parse(localStorage.getItem('gameHistory') || '[]');

    // 초기 히스토리 로드
    function loadHistory() {
        if (bestTime) {
            bestTimeValue.textContent = formatTime(parseInt(bestTime));
        }
        updateHistoryList();
    }

    function updateHistoryList() {
        historyItems.innerHTML = '';
        gameHistory.slice(0, 10).forEach((record, index) => {
            const li = document.createElement('li');
            li.textContent = `${index + 1}. ${formatTime(record.time)} - ${record.result}`;
            li.style.color = record.result === '성공' ? '#00ff00' : '#ff0000';
            historyItems.appendChild(li);
        });
    }

    function formatTime(ms) {
        const seconds = Math.floor(ms / 1000);
        const milliseconds = ms % 1000;
        return `${seconds}.${milliseconds.toString().padStart(3, '0')}초`;
    }

    function addHistoryRecord(time, success) {
        const record = {
            time: time,
            result: success ? '성공' : '실패',
            date: new Date().toISOString()
        };

        gameHistory.unshift(record);
        if (gameHistory.length > 10) {
            gameHistory.pop();
        }

        localStorage.setItem('gameHistory', JSON.stringify(gameHistory));
        updateHistoryList();

        if (success && (!bestTime || time < bestTime)) {
            bestTime = time;
            localStorage.setItem('bestTime', time);
            bestTimeValue.textContent = formatTime(time);
        }
    }

    function generateButtons() {
        // 기존 버튼들 제거
        const existingButtons = gameArea.querySelectorAll('.number-button');
        existingButtons.forEach(button => button.remove());
        
        // 1부터 50까지의 버튼 생성
        for (let i = 1; i <= 50; i++) {
            const button = document.createElement('button');
            button.textContent = i;
            button.className = 'number-button';
            button.setAttribute('data-number', i);
            
            // 랜덤 위치 설정
            const gameAreaRect = gameArea.getBoundingClientRect();
            const buttonSize = 40; // 버튼의 크기
            const maxX = gameAreaRect.width - buttonSize;
            const maxY = gameAreaRect.height - buttonSize;
            
            const randomX = Math.random() * maxX;
            const randomY = Math.random() * maxY;
            
            button.style.left = `${randomX}px`;
            button.style.top = `${randomY}px`;
            
            button.addEventListener('click', () => handleClick(i));
            gameArea.appendChild(button);
        }
    }

    function updateCurrentButton() {
        // 이전 강조 버튼 제거
        const prevButton = document.querySelector('.number-button.current');
        if (prevButton) {
            prevButton.classList.remove('current');
        }
        // 현재 눌러야 할 버튼 강조
        const currentButton = document.querySelector(`button.number-button[data-number="${currentNumber}"]`);
        if (currentButton) {
            currentButton.classList.add('current');
        }
    }

    function handleClick(number) {
        if (!gameActive) return;
        
        if (number === currentNumber) {
            const button = document.querySelector(`button.number-button[data-number="${number}"]`);
            if (button) {
                button.style.visibility = 'hidden';
            }
            currentNumber--;
            
            if (currentNumber === 0) {
                endGame(true);
            } else {
                updateCurrentButton();
            }
        } else {
            endGame(false);
        }
    }

    function startCountdown() {
        startButton.style.display = 'none';
        let count = 3;
        countdownElement.textContent = count;
        
        const countInterval = setInterval(() => {
            count--;
            if (count > 0) {
                countdownElement.textContent = count;
            } else {
                clearInterval(countInterval);
                startSection.style.display = 'none';
                timerSection.style.display = 'block';
                startGame();
            }
        }, 1000);
    }

    function startGame() {
        gameActive = true;
        currentNumber = 50;
        startTime = Date.now();
        
        // 메시지 숨기기
        messageElement.classList.remove('success', 'show');
        
        // 타이머 시작
        let timeLeft = 30000; // 30초를 밀리초로 변환
        
        function updateTimer() {
            const seconds = Math.floor(timeLeft / 1000);
            const milliseconds = timeLeft % 1000;
            timerElement.textContent = `${seconds}.${milliseconds.toString().padStart(3, '0')}`;
        }
        
        updateTimer();
        
        timer = setInterval(() => {
            timeLeft -= 10; // 10ms 단위로 감소
            updateTimer();
            
            if (timeLeft <= 0) {
                endGame(false);
            }
        }, 10); // 10ms 간격으로 업데이트
        
        // 시작 섹션 숨기기
        startSection.style.display = 'none';
        
        generateButtons();
        updateCurrentButton();
    }

    function endGame(success) {
        gameActive = false;
        clearInterval(timer);
        const endTime = Date.now();
        const timeTaken = endTime - startTime;
        
        // 게임 영역의 버튼들 제거
        const buttons = gameArea.querySelectorAll('.number-button');
        buttons.forEach(button => button.remove());
        
        if (success) {
            messageElement.textContent = '성공! 서버에서 결과를 확인중입니다...';
            messageElement.classList.add('show');
            addHistoryRecord(timeTaken, true);
            
            fetch('question39.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ success: true })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    messageElement.textContent = `성공! ${data.flag}`;
                    messageElement.classList.add('success', 'show');
                } else {
                    messageElement.textContent = `오류: ${data.message}`;
                    messageElement.classList.add('show');
                }
            })
            .catch(error => {
                messageElement.textContent = '서버 통신 중 오류가 발생했습니다.';
                messageElement.classList.add('show');
                console.error('Error:', error);
            });
        } else {
            messageElement.textContent = '실패! 다시 시도하세요.';
            messageElement.classList.add('show');
            addHistoryRecord(timeTaken, false);
            setTimeout(() => {
                location.reload();
            }, 2000);
        }
    }

    // 시작 버튼 이벤트 리스너
    startButton.addEventListener('click', () => {
        messageElement.classList.remove('success', 'show');
        startCountdown();
    });
    loadHistory(); // 초기 히스토리 로드
});
