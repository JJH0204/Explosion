document.addEventListener('DOMContentLoaded', function() {
    class AdminCardManager {
        constructor() {
            // localStorage에서 페이지 번호 가져오기
            const savedPage = localStorage.getItem('currentCardPage');
            
            this.state = {
                currentPage: savedPage ? parseInt(savedPage) : 0,
                cardsPerPage: 10,
                totalCards: 40,
                totalPages: Math.ceil(40 / 10),
                clearedStages: []
            };

            this.elements = {
                cardsWrapper: document.querySelector('.cards-wrapper'),
                popup: document.getElementById('challengePopup'),
                progressFill: document.querySelector('.progress-fill'),
                completedElement: document.getElementById('completed-challenges'),
                totalElement: document.getElementById('total-challenges')
            };

            this.setupPopupEvents();
        }

        async init() {
            try {
                await this.loadClearedStages();
                this.renderCards();
                this.setupArrowButtons();
                this.createPageButtons();
                this.updateProgress();
                
                // 저장된 페이지로 즉시 이동
                const savedPage = parseInt(localStorage.getItem('currentCardPage')) || 0;
                this.setCurrentPage(savedPage);
            } catch (error) {
                console.error('초기화 실패:', error);
            }
        }

        async loadClearedStages() {
            const response = await fetch('/assets/php/get_cleared_stages.php');
            const result = await response.json();
            if (!result.success) {
                throw new Error(result.error);
            }
            this.state.clearedStages = result.data;
        }

        renderCards() {
            if (!this.elements.cardsWrapper) return;
            
            this.elements.cardsWrapper.innerHTML = '';
            
            for (let page = 0; page < this.state.totalPages; page++) {
                const grid = document.createElement('div');
                grid.className = 'cards-grid';
                
                const startCard = page * this.state.cardsPerPage + 1;
                const endCard = Math.min((page + 1) * this.state.cardsPerPage, this.state.totalCards);
                
                for (let i = startCard; i <= endCard; i++) {
                    grid.appendChild(this.createCard(i));
                }
                
                this.elements.cardsWrapper.appendChild(grid);
            }
        }

        createCard(cardNumber) {
            const card = document.createElement('div');
            card.className = 'challenge-card';
            card.dataset.cardNumber = cardNumber;
            
            const img = document.createElement('img');
            img.src = this.state.clearedStages.includes(cardNumber) 
                ? `/assets/images/monsters/monster_image${cardNumber}.png`
                : '/assets/images/card_back.jpg';
            img.alt = `Card ${cardNumber}`;
            
            card.appendChild(img);
            
            card.addEventListener('click', async () => {
                try {
                    // config.json 불러오기
                    const configResponse = await fetch('/data/config.json');
                    const config = await configResponse.json();
                    
                    // 해당 카드의 챌린지 정보 찾기
                    const challengeInfo = config.challenges.find(c => c.id === cardNumber);
                    if (!challengeInfo) {
                        throw new Error('Challenge not found');
                    }

                    // 마크다운 파일 불러오기
                    const markdownResponse = await fetch(`/data/challenges/mk_${cardNumber}.md`);
                    if (!markdownResponse.ok) {
                        throw new Error('마크다운 파일을 불러올 수 없습니다.');
                    }
                    const markdownContent = await markdownResponse.text();

                    const popup = document.getElementById('challengePopup');
                    
                    // 팝업 내용 구성
                    popup.innerHTML = `
                        <div class="challenge-popup-content">
                            <button class="close-button">&times;</button>
                            <h2 class="challenge-title">Challenge ${cardNumber}</h2>
                            
                            <div class="tags-container">
                                <span class="tag category-tag">${config.categories[challengeInfo.category].name}</span>
                                <span class="tag difficulty-tag">${config.difficulty[challengeInfo.difficulty].name}</span>
                                <span class="tag points-tag">${config.difficulty[challengeInfo.difficulty].points}pt</span>
                            </div>
                            
                            <div class="markdown-content">
                                ${marked.parse(markdownContent)}
                            </div>
                            
                            <div class="flag-input-container">
                                <input type="text" 
                                    id="flagInput-${cardNumber}" 
                                    name="flagInput" 
                                    class="flag-input" 
                                    placeholder="플래그를 입력하세요">
                                <button class="action-button submit">제출</button>
                            </div>
                            
                            <button class="action-button challenge">문제 풀기</button>
                        </div>
                    `;
                    
                    popup.style.display = 'flex';
                    
                    // 닫기 버튼 이벤트
                    const closeButton = popup.querySelector('.close-button');
                    closeButton.addEventListener('click', () => {
                        popup.style.display = 'none';
                    });
                    
                    // 제출 버튼 이벤트
                    const submitButton = popup.querySelector('.action-button.submit');
                    if (submitButton) {
                        submitButton.addEventListener('click', async () => {
                            const flagInput = popup.querySelector(`#flagInput-${cardNumber}`);
                            if (!flagInput) return;

                            try {
                                // config.json에서 해당 문제의 정답 확인
                                const challengeInfo = config.challenges.find(c => c.id === cardNumber);
                                if (!challengeInfo) {
                                    throw new Error('Challenge not found');
                                }

                                // 입력된 플래그와 정답 비교
                                if (flagInput.value === challengeInfo.answer) {
                                    // 정답이 맞으면 saveClearedCard.php 호출
                                    const response = await fetch('/assets/php/saveClearedCard.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                        },
                                        body: `cardId=${cardNumber}`
                                    });

                                    const result = await response.json();
                                    if (result.success) {
                                        alert('축하합니다! 문제를 해결했습니다!');
                                        location.reload();
                                    } else {
                                        alert(result.error || '오류가 발생했습니다.');
                                    }
                                } else {
                                    alert('틀린 답입니다. 다시 시도해주세요.');
                                }
                            } catch (error) {
                                console.error('Error submitting flag:', error);
                                alert('플래그 제출 중 오류가 발생했습니다.');
                            }
                        });
                    }

                    // 챌린지 버튼 이벤트
                    const challengeButton = popup.querySelector('.action-button.challenge');
                    if (challengeButton) {
                        challengeButton.addEventListener('click', () => {
                            const questionUrl = `/Question/question${cardNumber}/question${cardNumber}.html`;
                            window.open(questionUrl, '_blank');
                        });
                    }
                } catch (error) {
                    console.error('Error loading challenge:', error);
                    alert('챌린지 로딩 중 오류가 발생했습니다.');
                }
            });
            
            return card;
        }

        setupArrowButtons() {
            const leftButton = document.querySelector('.arrow-button.left');
            const rightButton = document.querySelector('.arrow-button.right');

            if (leftButton) {
                leftButton.addEventListener('click', () => {
                    if (this.state.currentPage > 0) {
                        this.setCurrentPage(this.state.currentPage - 1);
                    }
                });
            }

            if (rightButton) {
                rightButton.addEventListener('click', () => {
                    if (this.state.currentPage < this.state.totalPages - 1) {
                        this.setCurrentPage(this.state.currentPage + 1);
                    }
                });
            }
        }

        updateButtons() {
            const leftButton = document.querySelector('.arrow-button.left');
            const rightButton = document.querySelector('.arrow-button.right');

            if (leftButton) {
                leftButton.style.opacity = this.state.currentPage === 0 ? '0.5' : '1';
                leftButton.style.pointerEvents = this.state.currentPage === 0 ? 'none' : 'auto';
            }

            if (rightButton) {
                rightButton.style.opacity = this.state.currentPage === this.state.totalPages - 1 ? '0.5' : '1';
                rightButton.style.pointerEvents = this.state.currentPage === this.state.totalPages - 1 ? 'none' : 'auto';
            }

            this.updatePageButtons();
        }

        createPageButtons() {
            const pageButtonsContainer = document.querySelector('.page-buttons');
            if (!pageButtonsContainer) return;
            
            pageButtonsContainer.innerHTML = '';
            
            for (let i = 0; i < this.state.totalPages; i++) {
                const button = document.createElement('button');
                button.className = 'page-button';
                if (i === this.state.currentPage) {
                    button.classList.add('active');
                }
                button.textContent = i + 1;
                
                button.addEventListener('click', () => {
                    this.setCurrentPage(i);
                });
                
                pageButtonsContainer.appendChild(button);
            }
        }

        updatePageButtons() {
            const pageButtons = document.querySelectorAll('.page-button');
            pageButtons.forEach((button, index) => {
                if (index === this.state.currentPage) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            });
        }

        updateProgress() {
            const progressFill = document.querySelector('.progress-fill');
            if (!progressFill) {
                console.error('Progress fill element not found');
                return;
            }

            const completedElement = document.getElementById('completed-challenges');
            if (!completedElement) {
                console.error('Completed challenges element not found');
                return;
            }

            const validStage = (!isNaN(this.state.currentPage) && this.state.currentPage !== null && this.state.currentPage !== '') ? parseInt(this.state.currentPage) : 0;
            
            const totalStages = 40;
            completedElement.textContent = validStage || '-';  // 유효한 값이 없으면 '-' 표시
            progressFill.style.width = `${(validStage / totalStages) * 100}%`;  // 유효하지 않으면 0%
        }

        updateCardImage(cardNumber) {
            const cards = document.querySelectorAll('.challenge-card');
            cards.forEach(card => {
                if (parseInt(card.dataset.cardNumber) === cardNumber) {
                    const img = card.querySelector('img');
                    if (img) {
                        img.src = `/assets/images/monsters/monster_image${cardNumber}.png`;
                        img.alt = `Monster ${cardNumber}`;
                    }
                }
            });
        }

        setupCardClickEvents() {
            const cards = document.querySelectorAll('.challenge-card');
            cards.forEach((card, index) => {
                card.addEventListener('click', () => {
                    this.openPopup(index + 1);
                });
            });
        }

        initializeCompletedChallenges() {
            const completedChallenges = JSON.parse(localStorage.getItem('completedChallenges') || '[]');
            completedChallenges.forEach(challengeNumber => {
                this.updateCardImage(challengeNumber);
            });
            this.updateProgress(0); // 진행 상황 초기화
        }

        setupPopupEvents() {
            const popup = document.getElementById('challengePopup');
            
            // 팝업 외부 클릭 시 닫기
            popup.addEventListener('click', (e) => {
                // 팝업의 배경(overlay)을 클릭했을 때만 닫기
                if (e.target === popup) {
                    popup.style.display = 'none';
                }
            });

            // ESC 키를 눌렀을 때도 팝업 닫기 (선택적)
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && popup.style.display === 'flex') {
                    popup.style.display = 'none';
                }
            });
        }

        setCurrentPage(pageNumber) {
            // 유효한 페이지 번호인지 확인
            if (pageNumber >= 0 && pageNumber < this.state.totalPages) {
                this.state.currentPage = pageNumber;
                localStorage.setItem('currentCardPage', pageNumber);
                
                // 카드 컨테이너 이동
                if (this.elements.cardsWrapper) {
                    this.elements.cardsWrapper.style.transform = `translateX(-${pageNumber * 100}%)`;
                }
                
                // 페이지 버튼 업데이트
                this.updatePageButtons();
            }
        }
    }

    async function updateRanking() {
        try {
            const response = await fetch('/assets/php/ranking.php');
            const data = await response.json();
            
            if (!data.success) {
                console.error('Failed to fetch ranking data:', data.error);
                return;
            }

            const rankingList = document.getElementById('rankingList');
            if (!rankingList) {
                console.error('Ranking list element not found');
                return;
            }
            
            rankingList.innerHTML = '';
            
            data.rankings.slice(0, 7).forEach((player, index) => {
                const li = document.createElement('li');
                li.className = 'ranking-item';
                
                let rankDisplay;
                if (index < 3) {
                    const medals = ['🥇', '🥈', '🥉'];
                    rankDisplay = medals[index];
                } else {
                    rankDisplay = index + 1;
                }
                
                li.innerHTML = `
                    <span class="rank">${rankDisplay}</span>
                    <div class="player-info">
                        <span class="nickname">${player.nickname}</span>
                        <span class="score">${player.score}pt</span>
                    </div>
                `;
                rankingList.appendChild(li);
            });
        } catch (error) {
            console.error('Error fetching ranking data:', error);
        }
    }

    async function updateUserInfo() {
        try {
            const response = await fetch('/assets/php/user_info.php');
            const data = await response.json();
            
            if (!data.success) {
                console.error('Failed to fetch user info:', data.error);
                return;
            }

            // DOM 요소들이 존재하는지 확인
            const nicknameElement = document.getElementById('player-nickname');
            const levelElement = document.getElementById('current-level');
            const scoreElement = document.getElementById('player-score');
            const progress = document.getElementById('progress');
            const completedElement = document.getElementById('completed-challenges');
            const totalElement = document.getElementById('total-challenges');

            // 각 요소가 존재할 때만 업데이트
            if (nicknameElement) {
                nicknameElement.textContent = data.data.nickname;
            }
            if (levelElement) {
                levelElement.textContent = data.data.rank;
            }
            if (scoreElement) {
                scoreElement.textContent = data.data.score;
            }
            
            // 진행 상황 업데이트
            if (progress && completedElement && totalElement) {
                const completed = data.data.stage;
                const total = 40; // 총 스테이지 수
                
                progress.style.setProperty('--completed', completed);
                progress.style.setProperty('--total', total);
                
                completedElement.textContent = completed;
                totalElement.textContent = total;
            }

        } catch (error) {
            console.error('Error fetching user info:', error);
        }
    }

    // 팝업 이벤트 설정
    function setupPopups() {
        // 플래그 버튼
        const flagBtn = document.getElementById('flagBtn');
        const flagPopup = document.getElementById('flagPopup');
        const cancelFlag = document.getElementById('cancelFlag');

        if (flagBtn && flagPopup) {
            flagBtn.addEventListener('click', () => flagPopup.style.display = 'flex');
        }
        if (cancelFlag) {
            cancelFlag.addEventListener('click', () => flagPopup.style.display = 'none');
        }

        // 이벤트 버튼
        const eventBtn = document.getElementById('EventBtn');
        const eventPopup = document.getElementById('eventPopup');
        const closeEvent = document.getElementById('closeEvent');

        if (eventBtn && eventPopup) {
            eventBtn.addEventListener('click', () => {
                eventPopup.style.display = 'flex';
            });
        }
        if (closeEvent) {
            closeEvent.addEventListener('click', () => {
                eventPopup.style.display = 'none';
            });
        }

        // 로그아웃 버튼
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', () => {
                if (confirm('로그아웃 하시겠습니까?')) {
                    window.location.href = 'login.html';
                }
            });
        }
    }

    // 초기화
    const adminCardManager = new AdminCardManager();
    adminCardManager.init();
    setupPopups();
    updateUserInfo();
    updateRanking();
    setInterval(updateRanking, 180000);

    // user_info.php에서 데이터를 가져와서 진행 상태 업데이트
    fetch('/assets/php/user_info.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const stage = parseInt(data.data.stage);
                const progressFill = document.querySelector('.progress-fill');
                const completedElement = document.getElementById('completed-challenges');
                
                if (progressFill && completedElement) {
                    completedElement.textContent = stage;
                    progressFill.style.width = `${(stage / 40) * 100}%`;
                }
            }
        })
        .catch(error => console.error('Error:', error));
}); 