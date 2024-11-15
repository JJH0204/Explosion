class GameManager {
    constructor() {
        this.questionsData = null;
        this.solvedCards = new Set(); // 클리어된 카드 저장
        this.initialize();
    }

    async initialize() {
        try {
            await this.loadQuestionData(); // 문제 데이터 로드
            this.initializeEventListeners(); // 이벤트 리스너 초기화
        } catch (error) {
            console.error('Failed to initialize GameManager:', error);
        }
    }

    // loadQuestionData 메서드 추가
    async loadQuestionData() {
        try {
            const response = await fetch('./data/config.json'); // config.json 파일에서 데이터 로드
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            if (!data || !data.challenges) {
                throw new Error('Invalid data structure in config.json');
            }
            this.questionsData = data;
        } catch (error) {
            console.error('Error loading questions data:', error);
            this.questionsData = { challenges: [] };
        }
    }

    async revealGame(cardElement, gameId) {
        console.log('Revealing game:', gameId);
        
        const popup = document.getElementById('gamePopup');
        if (!popup) {
            console.error('Popup element not found');
            return;
        }

        popup.style.display = 'block';
        popup.dataset.currentCardId = cardElement.dataset.id;

        const gameContent = document.getElementById('gameContent');
        if (!gameContent) {
            console.error('Game content element not found');
            return;
        }

        gameContent.innerHTML = '<div class="loading">로딩 중...</div>';

        try {
            if (!this.questionsData) {
                await this.loadQuestionData();
            }

            const cardId = parseInt(cardElement.dataset.id);
            const challenge = this.questionsData.challenges.find(c => c.id === cardId);

            if (!challenge) {
                throw new Error(`Challenge not found for card ${cardId}`);
            }

            const markdownContent = await this.loadMarkdownContent(cardId);
            
            gameContent.innerHTML = `
                <div class="game-description">
                    <h2>${challenge.title}</h2>
                    <p>${challenge.description}</p>
                    ${markdownContent}
                </div>
                <div class="flag-input">
                    <label for="flagInput">플래그</label>
                    <input type="text" id="flagInput" placeholder="플래그를 입력하세요">
                </div>
                <div class="game-buttons">
                    <button class="challenge-button" data-card-id="${challenge.id}">
                        ${challenge.link ? '도전하기' : '준비중'}
                    </button>
                    <button class="submit-button" data-game-id="${gameId}">정답 제출</button>
                </div>
            `;
        } catch (error) {
            console.error('Error in revealGame:', error);
            gameContent.innerHTML = `
                <div class="error">
                    문제 내용을 불러올 수 없습니다.<br>
                    <small>${error.message}</small>
                </div>
            `;
        }
    }

    async loadMarkdownContent(cardId) {
        try {
            const challenge = this.questionsData.challenges.find(c => c.id === cardId);
            if (!challenge) {
                throw new Error(`Challenge not found for card ${cardId}`);
            }

            const response = await fetch(`./data/challenges/${challenge.markdownFile}`);
            if (!response.ok) {
                throw new Error('Failed to load markdown content');
            }

            const markdownText = await response.text();
            return marked.parse(markdownText);
        } catch (error) {
            console.error('Error loading markdown:', error);
            return '<p class="error">마크다운 내용을 불러올 수 없습니다.</p>';
        }
    }

    // 데이터베이스에 기록하는 메서드 추가
    async saveToDatabase(cardId, nickname) {
        console.log('Saving to database with nickname:', nickname, 'and cardId:', cardId); // 디버깅 로그 추가
        try {
            const response = await fetch('./assets/php/saveScore.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    cardId: cardId,
                    nickname: nickname,
                }),
            });
    
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
    
            const result = await response.json();
            if (!result.success) {
                throw new Error(result.error || 'Unknown error');
            }
        } catch (error) {
            console.error('Error saving to database:', error);
            throw error;
        }
    }
    
    

    // 세션에서 닉네임 가져오는 메서드 수정
    async getNicknameFromSession() {
        try {
            const response = await fetch('./assets/php/user_info.php');
            if (!response.ok) {
                throw new Error('Failed to fetch user info');
            }
            const data = await response.json();
            console.log('Fetched nickname:', data.username); // 디버깅 로그 추가
            if (data.username) {
                return data.username;
            }
            throw new Error(data.error || 'Unknown error');
        } catch (error) {
            console.error('Error fetching nickname from session:', error);
            return 'Guest'; // 오류 시 기본값 반환
        }
    }


    initializeClearedCards(clearedCards) {
        console.log('initializeClearedCards this:', this); // this 확인
        this.solvedCards = new Set(clearedCards);
        this.restoreClearedCards();
        this.updateProgress();
    }

    restoreClearedCards() {
        this.solvedCards.forEach(cardId => {
            const card = document.querySelector(`.card[data-id="${cardId}"]`);
            if (card) {
                this.activateCard(card);
            }
        });
    }

    startChallenge(cardId) {
        try {
            console.log('Starting challenge for cardId:', cardId);
            
            const challenge = this.questionsData.challenges.find(
                c => c.id === parseInt(cardId)
            );
            
            console.log('Found challenge:', challenge);

            if (!challenge) {
                throw new Error(`Challenge not found for card ${cardId}`);
            }

            if (challenge.link && challenge.link.trim() !== '') {
                window.location.href = challenge.link;
            } else {
                alert('이 문제는 아직 준비되지 않았습니다.');
            }
        } catch (error) {
            console.error('Error starting challenge:', error);
            alert('문제를 시작할 수 없습니다.');
        }
    }

    async submitFlag(gameId) {
        const flagInput = document.getElementById('flagInput');
        if (!flagInput) {
            alert('플래그 입력 필드를 찾을 수 없습니다.');
            return;
        }
    
        const flag = flagInput.value.trim();
        if (!flag) {
            alert('플래그를 입력해주세요.');
            return;
        }
    
        if (this.submitting) {
            console.warn('Already submitting, please wait.');
            return;
        }
        this.submitting = true;
    
        try {
            const cardId = parseInt(gameId.replace('game', ''));
            const challenge = this.questionsData.challenges.find(c => c.id === cardId);
    
            if (!challenge) {
                throw new Error('Challenge not found');
            }
    
            if (flag === challenge.answer) {
                this.handleCorrectAnswer(cardId);
    
                // 닉네임 가져오기 및 저장
                const nickname = await this.getNicknameFromSession();
                console.log('Nickname used for saving:', nickname); // 디버깅 로그 추가
                await this.saveToDatabase(cardId, nickname);
            } else {
                alert('틀렸습니다. 다시 시도해주세요.');
            }
        } catch (error) {
            console.error('Error submitting flag:', error);
        } finally {
            this.submitting = false;
        }
    }
    
    
    

    handleCorrectAnswer(cardId) {
        alert('축하합니다! 정답입니다!');
        
        const card = document.querySelector(`.card[data-id="${cardId}"]`);
        if (!card) {
            console.error('Card element not found');
            return;
        }

        this.activateCard(card);
        
        this.closePopup();
    }

    activateCard(card) {
        const cardId = card.dataset.id;
        this.solvedCards.add(cardId);

        card.classList.add('solved');

        const cardInner = card.querySelector('.card-inner');
        if (cardInner) {
            cardInner.style.transform = 'rotateY(180deg)';
        }

        const cardBack = card.querySelector('.card-back');
        if (cardBack) {
            cardBack.innerHTML = `
                <img src="${CONFIG.PATHS.IMAGES}monster_image${cardId}.png" 
                     alt="Monster ${cardId}" 
                     class="monster-image">
            `;
        }
    }

    // updateProgress 메서드 추가
    updateProgress() {
        console.log('updateProgress this:', this); // this 확인
        const completedElement = document.getElementById('completed-challenges');
        const totalElement = document.getElementById('total-challenges');
    
        if (completedElement && totalElement) {
            completedElement.textContent = this.solvedCards.size;
            totalElement.textContent = CONFIG.GAME.TOTAL_CARDS;
        }
    }

    initializeEventListeners() {
        document.addEventListener('click', (e) => {
            const target = e.target;

            if (target.classList.contains('challenge-button')) {
                const cardId = target.dataset.cardId;
                this.startChallenge(cardId);
            }

            if (target.classList.contains('submit-button')) {
                const gameId = target.dataset.gameId;
                this.submitFlag(gameId);
            }
        });

        const popup = document.getElementById('gamePopup');
        popup.addEventListener('click', (e) => {
            if (e.target.closest('.popup-content')) {
                return;
            }
            if (e.target === popup) {
                this.closePopup();
            }
        });
    }

    closePopup() {
        const popup = document.getElementById('gamePopup');
        popup.style.display = 'none';
    }

    loadSolvedCards() {
        const savedCards = localStorage.getItem('solvedCards');
        if (savedCards) {
            this.solvedCards = new Set(JSON.parse(savedCards));
        }
    }
    
}
