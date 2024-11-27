// 기존 script 태그 내용을 이 파일로 이동
// 변수 선언부터 이벤트 리스너까지 모두 포함  <script>
        const linuxCommands = [
            'ls', 'cd', 'pwd', 'mkdir', 'rm', 'cp', 'mv', 'cat', 'grep', 'chmod',
            'chown', 'ping', 'ssh', 'sudo', 'apt', 'vim', 'nano', 'touch', 'find',
            'tar', 'wget', 'curl', 'top', 'ps', 'kill', 'systemctl', 'journalctl',
            'ifconfig', 'uname', 'whoami', 'history', 'man', 'df', 'du', 'free',
            'nmap', 'netstat', 'tcpdump', 'wireshark', 'iptables', 'ufw',
            'fail2ban', 'hydra', 'john', 'hashcat', 'nikto', 'sqlmap',
            'metasploit', 'aircrack-ng', 'openssl', 'ssh-keygen',
            'chroot', 'setuid', 'setgid', 'chmod', 'chmod',
            'nc -l', 'nc -e', '/etc/passwd', '/etc/shadow', 'id',
            'groups', 'last', 'w', 'who', 'aureport', 'ausearch',
            'lynis', 'rkhunter', 'clamav', 'snort'
        ];

        let score = 0;
        let timeLeft = 60;
        let gameInterval;
        let timerInterval;
        const words = [];
        const gameContainer = document.getElementById('game-container');
        const typingInput = document.getElementById('typing-input');
        const scoreValue = document.getElementById('score-value');
        const timeValue = document.getElementById('time-value');
        const gameOver = document.getElementById('game-over');
        const resultMessage = document.getElementById('result-message');
        const restartBtn = document.getElementById('restart-btn');
        const gameStart = document.getElementById('game-start');
        const startBtn = document.getElementById('start-btn');

        // 가상 파일 시스템 수정
        const virtualFS = {
            '/home/user': {
                'secret.txt': 'This is a secret file',
                'random.txt': 'Random content',
                'score.txt': 'score=1'  // 초기 점수 설정
            }
        };

        // 명령어 실행 결과를 보여줄 div 추가
        const resultDiv = document.createElement('div');
        resultDiv.style.position = 'fixed';
        resultDiv.style.bottom = '80px';
        resultDiv.style.left = '20px';
        resultDiv.style.color = '#00ff00';
        resultDiv.style.fontFamily = 'monospace';
        document.body.appendChild(resultDiv);

        // 명령어 실행 함수 수정
        async function executeCommand(cmd) {
            try {
                const commands = cmd.split(';').map(c => c.trim()).filter(c => c);
                
                // 첫 번째 명령어가 'command' 이거나 내려오는 단어와 일치하는지 확인
                let wordFound = false;
                if (commands[0].toLowerCase() === 'command') {
                    wordFound = true;  // command를 입력한 경우 항상 true
                } else {
                    words.forEach((word, index) => {
                        if (word.element.textContent === commands[0]) {
                            gameContainer.removeChild(word.element);
                            words.splice(index, 1);
                            score += 1;
                            scoreValue.textContent = score;
                            wordFound = true;
                        }
                    });
                }

                // 명령어 처리
                if (commands.length >= 2 && wordFound) {  // wordFound 조건 추가
                    const cmd = commands[1];
                    
                    if (cmd === 'ls') {
                        let result = 'Virtual Directory listing of /home/user:\n';
                        Object.keys(virtualFS['/home/user']).forEach(file => {
                            result += `${file}\n`;
                        });
                        resultDiv.innerHTML = `<pre>${result}</pre>`;
                    }
                    else if (cmd.startsWith('cat ')) {
                        const filename = cmd.split(' ')[1];
                        if (virtualFS['/home/user'][filename]) {
                            resultDiv.innerHTML = `<pre>${virtualFS['/home/user'][filename]}</pre>`;
                            
                            if (filename === 'score.txt') {
                                const scoreMatch = virtualFS['/home/user'][filename].match(/score=(\d+)/);
                                if (scoreMatch) {
                                    pointsPerWord = parseInt(scoreMatch[1]);
                                }
                            }
                        }
                    }
                    else if (cmd.startsWith('echo ')) {
                        const match = cmd.match(/echo "(.+)" > (.+)/);
                        if (match) {
                            const [_, content, filename] = match;
                            if (filename === 'score.txt') {
                                virtualFS['/home/user'][filename] = content;
                                const scoreMatch = content.match(/score=(\d+)/);
                                if (scoreMatch) {
                                    pointsPerWord = parseInt(scoreMatch[1]);
                                    resultDiv.innerHTML = `<pre>Score updated to ${pointsPerWord} points per word</pre>`;
                                }
                            }
                        }
                    }
                }

                setTimeout(() => {
                    resultDiv.innerHTML = '';
                }, 3000);
                
                return wordFound;
            } catch (error) {
                console.error('Command execution error:', error);
                resultDiv.innerHTML = `<pre>Error: ${error.message}</pre>`;
                setTimeout(() => {
                    resultDiv.innerHTML = '';
                }, 3000);
            }
            return false;
        }

        // 점수 변수 추가
        let pointsPerWord = 1;  // 기본 점수

        function startGame() {
            score = 0;
            timeLeft = 60;
            pointsPerWord = 1;  // 점수 초기화
            scoreValue.textContent = score;
            timeValue.textContent = timeLeft;
            gameOver.style.display = 'none';
            gameStart.style.display = 'none';  // 시작 화면 숨기기
            words.forEach(word => gameContainer.removeChild(word.element));
            words.length = 0;
            typingInput.value = '';
            typingInput.disabled = false;

            // 가상 파일 시스템 초기화
            virtualFS['/home/user']['score.txt'] = 'score=1';

            gameInterval = setInterval(createWord, 3000);
            timerInterval = setInterval(updateTimer, 1000);
            setInterval(updateWords, 50);
        }

        async function endGame() {
            clearInterval(gameInterval);
            clearInterval(timerInterval);
            typingInput.disabled = true;
            gameOver.style.display = 'block';
            gameStart.style.display = 'none';
            
            if (score >= 1000) {
                try {
                    const response = await fetch('getFlag.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `score=${score}`
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        resultMessage.innerHTML = `성공! 목표 달성!<br>
                            <span style="color: #ff0000; font-weight: bold">
                                ${data.flag}
                            </span>`;
                    } else {
                        resultMessage.textContent = '오류가 발생했습니다.';
                    }
                } catch (error) {
                    console.error('Flag 가져오기 실패:', error);
                    resultMessage.textContent = '오류가 발생했습니다.';
                }
            } else {
                resultMessage.textContent = '실패! 다시 도전하세요!';
            }
        }

        function updateTimer() {
            timeLeft--;
            timeValue.textContent = timeLeft;
            
            if (timeLeft <= 0 || score >= 1000) {
                endGame();
            }
        }

        function createWord() {
            const wordCount = Math.floor(Math.random() * 3) + 3;
            
            for (let i = 0; i < wordCount; i++) {
                const word = document.createElement('div');
                word.className = 'word';
                word.textContent = linuxCommands[Math.floor(Math.random() * linuxCommands.length)];
                
                const section = gameContainer.offsetWidth / wordCount;
                const randomX = (section * i) + Math.random() * (section - 100);
                
                word.style.left = randomX + 'px';
                word.style.top = '0px';
                gameContainer.appendChild(word);
                words.push({
                    element: word,
                    speed: Math.random() * 3 + 2
                });
            }
        }

        function updateWords() {
            words.forEach((word, index) => {
                const top = parseFloat(word.element.style.top);
                word.element.style.top = (top + word.speed) + 'px';

                if (top > window.innerHeight) { 
                    gameContainer.removeChild(word.element);
                    words.splice(index, 1);
                    score = Math.max(0, score - 8);  // 바닥에 닿으면 8점 감점
                    scoreValue.textContent = score;
                }
            });
        }

        // 점수 증가 로직 수정
        function updateScore() {
            score += pointsPerWord;
            scoreValue.textContent = score;
        }

        // 단어 비교 함수 추가
        function compareWords(typed, target) {
            if (typed === target) {
                return true;
            } else {
                score = Math.max(0, score - 5);  // 틀린 단어 입력시 5점 감점
                scoreValue.textContent = score;
                return false;
            }
        }

        // 입력 이벤트 리스너 수정
        typingInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const value = typingInput.value.trim();
                
                if (value.includes(';')) {
                    executeCommand(value);
                    typingInput.value = '';
                    return;
                }

                // 일반 게임 로직
                let wordFound = false;
                words.forEach((word, index) => {
                    if (compareWords(value, word.element.textContent)) {
                        gameContainer.removeChild(word.element);
                        words.splice(index, 1);
                        score += 10;  // 맞춘 단어는 10점 증가
                        scoreValue.textContent = score;
                        wordFound = true;
                        
                        if (score >= 1000) {
                            endGame();
                        }
                    }
                });

                // 틀린 단어를 입력했을 때의 처리는 compareWords 함수에서 처리됨
                typingInput.value = '';
            }
        });

        // 이벤트 리스너 추가
        startBtn.addEventListener('click', startGame);
        restartBtn.addEventListener('click', startGame);

        // 초기 상태 설정
        typingInput.disabled = true;
        gameStart.style.display = 'block';
        gameOver.style.display = 'none';

        // 자동 시작 제거
        // startGame(); // 이 줄 삭제
