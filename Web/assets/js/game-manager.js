class GameManager {
    constructor() {
        this.questionsData = null;
        this.solvedCards = new Set(); // 클리어된 카드 저장
        this.initialize();
        this.loadSolvedCards(); // 페이지 로드 시 클리어된 카드 정보 불러오기
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
                    <button class="submit-button" id="submitFlagBtn" data-game-id="${gameId}">정답 제출</button>
                </div>
            `;

            // 버튼에 직접 이벤트 리스너 추가
            const submitButton = gameContent.querySelector('#submitFlagBtn');
            if (submitButton) {
                submitButton.addEventListener('click', () => {
                    this.submitFlag(gameId);
                }, { once: true }); // once: true를 추가하여 한 번만 실행되도록 설정
            }
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
        console.log('Saving to database with nickname:', nickname, 'and cardId:', cardId);
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
            if (data.data && data.data.nickname) {
                return data.data.nickname;
            }
            throw new Error(data.error || 'Unknown error');
        } catch (error) {
            console.error('Error fetching nickname from session:', error);
            return 'Guest'; // 오류 시 기본값 반환
        }
    }
    

    initializeClearedCards(clearedCards) {
        if (Array.isArray(clearedCards)) {
            clearedCards.forEach(cardId => {
                this.solvedCards.add(cardId.toString());
            });
            this.updateProgress(); // 진행 상황 업데이트
            localStorage.setItem('solvedCards', JSON.stringify([...this.solvedCards])); // 클리어된 카드 정보를 localStorage에 저장
        }
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
                window.open(challenge.link, '_blank');
            } else {
                alert('이 문제는 아직 준비되지 않았습니다.');
            }
        } catch (error) {
            console.error('Error starting challenge:', error);
            alert('문제를 시작할 수 없습니다.');
        }
    }

    async submitFlag(gameId) {
        if (this.isSubmitting) {
            return;
        }

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

        this.isSubmitting = true;

        try {
            const cardId = parseInt(gameId.replace('game', ''));
            const challenge = this.questionsData.challenges.find(c => c.id === cardId);

            if (!challenge) {
                throw new Error('Challenge not found');
            }

            let isCorrect;
            
            // 25번 문제일 경우 특별 처리
            if (cardId === 25) {
                try {
                    // 현재 사용자의 FLAG 값을 조회하는 쿼리
                    const response = await fetch('Question/question25/execute_query.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `query=SELECT FLAG FROM ${await this.getNicknameFromSession()}`
                    });
                    
                    const result = await response.json();
                    if (result.error) {
                        throw new Error(result.error);
                    }
                    
                    // DB에서 가져온 FLAG 값과 입력값 비교
                    if (result.data && result.data[0] && result.data[0].FLAG === flag) {
                        isCorrect = true;
                    } else {
                        isCorrect = false;
                    }
                } catch (error) {
                    console.error('Error checking flag for challenge 25:', error);
                    throw error;
                }
            } else {
                // 다른 문제들은 기존 방식대로 처리
                isCorrect = flag === challenge.answer;
            }

            if (isCorrect) {
                const nickname = await this.getNicknameFromSession();
                await this.saveToDatabase(cardId, nickname);
                await this.handleCorrectAnswer(cardId);
                window.location.reload();
            } else {
                alert('틀렸습니다. 다시 시도해주세요.');
                flagInput.value = '';
            }
        } catch (error) {
            console.error('Error submitting flag:', error);
            alert('오류가 발생했습니다. 다시 시도해주세요.');
        } finally {
            this.isSubmitting = false;
        }
    }

    async handleCorrectAnswer(cardId) {
        try {
            alert('축하합니다! 정답입니다!');
            
            const card = document.querySelector(`.card[data-id="${cardId}"]`);
            if (!card) {
                console.error('Card element not found');
                return;
            }

            // 카드 활성화
            this.activateCard(card);
            
            try {
                // 게임 요청 설정
                const requestResponse = await fetch('assets/php/set_game_request.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    credentials: 'include'
                });
                
                const requestData = await requestResponse.json();
                if (requestData.error) {
                    throw new Error(requestData.error);
                }

                // 점수 업데이트
                const scoreResponse = await fetch('assets/php/Scoreboard2.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    credentials: 'include'
                });
                
                const scoreData = await scoreResponse.json();
                if (scoreData.error) {
                    throw new Error(scoreData.error);
                }

                // 진행 상황 즉시 업데이트
                const completedElement = document.getElementById('completed-challenges');
                if (completedElement) {
                    // solvedCards Set의 크기를 사용하여 업데이트
                    completedElement.textContent = this.solvedCards.size;
                }

                // 다른 정보들도 업데이트
                if (typeof updateUserInfo === 'function') {
                    await updateUserInfo();
                }
                if (typeof updateRanking === 'function') {
                    await updateRanking();
                }

                // 팝업 닫기
                const popup = document.getElementById('gamePopup');
                if (popup) {
                    popup.style.display = 'none';
                }

            } catch (error) {
                console.error('Failed to update game progress:', error);
                throw error;
            }

        } catch (error) {
            console.error('Error in handleCorrectAnswer:', error);
            alert('점수 업데이트 중 오류가 발생했습니다. 새로고침 후 다시 시도해주세요.');
        }
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

        // 진행 상황 즉시 업데이트
        this.updateProgress();

        localStorage.setItem('solvedCards', JSON.stringify([...this.solvedCards]));
    }

    // updateProgress 메서드 추가
    updateProgress() {
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
