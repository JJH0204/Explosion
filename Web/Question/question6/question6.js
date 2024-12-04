class ReactionTest {
    constructor() {
        this.target = document.getElementById('target');
        this.result = document.getElementById('result');
        this.bestScore = document.getElementById('bestScore');
        this.avgScore = document.getElementById('avgScore');
        this.history = document.getElementById('history');
        this.resetBtn = document.getElementById('resetBtn');

        this.waitingTime = 0;
        this.startTime = 0;
        this.scores = [];
        this.timeoutId = null;
        this.gameState = 'waiting';

        // 이벤트 핸들러를 클래스 인스턴스에 바인딩
        this.handleClick = this.handleClick.bind(this);
        this.checkTooSoon = this.checkTooSoon.bind(this);
        this.reset = this.reset.bind(this);

        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // 메인 클릭 이벤트
        this.target.addEventListener('click', this.handleClick);
        // 리셋 버튼 이벤트
        this.resetBtn.addEventListener('click', this.reset);
    }

    handleClick() {
        switch(this.gameState) {
            case 'waiting':
                this.startGame();
                break;
            case 'ready':
                this.recordTime();
                break;
            case 'toosoon':
            case 'finished':
                this.reset();
                break;
        }
    }

    startGame() {
        // 이전 게임의 타이머가 있다면 제거
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
            this.timeoutId = null;
        }

        this.gameState = 'preparing';
        this.target.className = 'waiting';
        this.target.textContent = '준비...';
        this.result.textContent = '기다리세요...';
        
        // 1~5초 사이의 랜덤한 시간 후에 시작
        this.waitingTime = Math.floor(Math.random() * 4000) + 1000;
        
        this.timeoutId = setTimeout(() => {
            if (this.gameState === 'preparing') {
                this.gameState = 'ready';
                this.startTime = Date.now();
                this.target.className = 'ready';
                this.target.textContent = '클릭하세요!';
            }
        }, this.waitingTime);

        // 너무 일찍 클릭하는 것을 감지
        this.target.addEventListener('click', this.checkTooSoon);
    }

    checkTooSoon(event) {
        if (this.gameState === 'preparing') {
            if (this.timeoutId) {
                clearTimeout(this.timeoutId);
                this.timeoutId = null;
            }

            this.gameState = 'toosoon';
            this.target.className = 'too-soon';
            this.target.textContent = '너무 일찍 클릭했습니다!';
            this.result.textContent = '다시 시도하려면 클릭하세요';
            
            // 이벤트 리스너 제거
            this.target.removeEventListener('click', this.checkTooSoon);
        }
    }

    recordTime() {
        if (this.gameState !== 'ready') return;

        const endTime = Date.now();
        const reactionTime = endTime - this.startTime;
        
        this.gameState = 'finished';
        this.target.style.backgroundColor = '#3498db';
        this.target.textContent = `${reactionTime}ms`;
        this.target.classList.remove('ready');
        
        // 점수 기록 및 통계 업데이트
        this.scores.push(reactionTime);
        this.updateStats();
        
        // 서버에 점수 전송
        fetch('index.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ score: reactionTime })
        })
        .then(response => response.json())
        .then(data => {
            if (data.flag) {
                this.result.innerHTML = `축하합니다! Flag를 획득하셨습니다:<br>${data.flag}`;
                this.result.style.color = '#27ae60';
            }
        })
        .catch(error => console.error('Error:', error));
    }

    updateStats() {
        // 최근 5개의 기록만 유지
        if (this.scores.length > 5) {
            this.scores = this.scores.slice(-5);
        }

        // 최고 기록
        const best = Math.min(...this.scores);
        this.bestScore.textContent = best;

        // 평균 기록
        const avg = Math.round(this.scores.reduce((a, b) => a + b, 0) / this.scores.length);
        this.avgScore.textContent = avg;

        // 기록 히스토리 업데이트
        this.history.innerHTML = this.scores
            .slice()
            .reverse()
            .map(score => `<div class="history-item">${score}ms</div>`)
            .join('');

        // 가장 최근 기록 표시
        this.result.textContent = `${this.scores[this.scores.length - 1]}ms`;
    }

    reset() {
        // 진행 중인 타이머 제거
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
            this.timeoutId = null;
        }
        
        // 모든 이벤트 리스너 제거 후 다시 추가
        this.target.removeEventListener('click', this.checkTooSoon);
        
        // 초기 상태로 복구
        this.gameState = 'waiting';
        this.target.className = 'waiting';
        this.target.textContent = '시작하려면 클릭하세요';
        this.result.textContent = '준비되면 클릭하세요';
    }

    // 게임 종료 및 정리
    cleanup() {
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
        }
        this.target.removeEventListener('click', this.handleClick);
        this.target.removeEventListener('click', this.checkTooSoon);
        this.resetBtn.removeEventListener('click', this.reset);
    }
}

// 게임 인스턴스 생성
let game;
document.addEventListener('DOMContentLoaded', () => {
    // 이전 게임 인스턴스가 있다면 정리
    if (game) {
        game.cleanup();
    }
    game = new ReactionTest();
});