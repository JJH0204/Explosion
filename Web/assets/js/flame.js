// flame 페이지 동작 js
marked.setOptions({
    breaks: true,     
    gfm: true,        
    headerIds: true,   
    mangle: false,
});

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

            // localStorage에서 클리어한 스테이지 정보도 가져오기
            const savedClearedStages = localStorage.getItem('clearedStages');
            this.state.clearedStages = savedClearedStages ? JSON.parse(savedClearedStages) : [];

            this.setupPopupEvents();

            // 관리자 모드 키 조합 감지 (Ctrl + Shift + A)
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.shiftKey && e.key === 'A') {
                    this.openAdminMode();
                }
            });

            // localStorage 변경 감지
            window.addEventListener('storage', this.checkStorageModification.bind(this));
            // 초기 로드 시에도 체크
            this.checkStorageModification();

            // 진행 상태 업데이트는 한 번만 실행되도록 수정
            this.progressInitialized = false;
        }

        checkStorageModification() {
            const clearedStages = JSON.parse(localStorage.getItem('clearedStages') || '[]');
            const allStages = Array.from({length: 40}, (_, i) => i + 1);
            
            // 모든 스테이지가 클리어된 것으로 표시되었는지 확인
            const isAllCleared = allStages.every(stage => clearedStages.includes(stage));
            
            if (isAllCleared) {
                // DB에서 실제 클리어한 스테이지 수 확인
                fetch('/assets/php/getClearedStages.php')
                    .then(response => response.json())
                    .then(result => {
                        if (result.success && result.data.length < 40) {
                            // 실제로는 모든 스테이지를 클리어하지 않았는데 localStorage가 수정된 경우
                            alert('축하합니다! 플래그를 찾았습니다: Flag{LocalStorage_Modification}');
                        }
                    })
                    .catch(error => console.error('Error checking stages:', error));
            }
        }

        async init() {
            try {
                await this.loadClearedStages();
                this.renderCards();
                this.setupArrowButtons();
                this.createPageButtons();
                
                // 진행 상태 초기화는 한 번만 실행
                if (!this.progressInitialized) {
                    await this.initializeProgress();
                    this.progressInitialized = true;
                }
                
                // 저장된 페이지로 즉시 이동
                const savedPage = parseInt(localStorage.getItem('currentCardPage')) || 0;
                this.setCurrentPage(savedPage);
            } catch (error) {
                console.error('초기화 실패:', error);
            }
        }

        async loadClearedStages() {
            try {
                const response = await fetch('/assets/php/getClearedStages.php');
                const result = await response.json();
                if (!result.success) {
                    throw new Error(result.error);
                }

                // localStorage에 저장된 기존 데이터 가져오기
                const localStages = JSON.parse(localStorage.getItem('clearedStages') || '[]');
                
                // DB의 데이터와 로컬 데이터 합치기 (중복 제거)
                const mergedStages = [...new Set([...result.data, ...localStages])].sort((a, b) => a - b);
                
                // state와 localStorage 업데이트
                this.state.clearedStages = mergedStages;
                localStorage.setItem('clearedStages', JSON.stringify(mergedStages));
            } catch (error) {
                console.error('Error loading cleared stages:', error);
                // 에러 발생 시 localStorage의 데이터만이라도 사용
                const localStages = JSON.parse(localStorage.getItem('clearedStages') || '[]');
                this.state.clearedStages = localStages;
            }
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

            const cardInner = document.createElement('div');
            cardInner.className = 'challenge-card-inner';

            const img = document.createElement('img');
            img.src = this.state.clearedStages.includes(cardNumber) 
                ? `/assets/images/monsters/monster_image${cardNumber}.png`
                : '/assets/images/flame_card.jpg';
            img.alt = `Card ${cardNumber}`;

            cardInner.appendChild(img);
            card.appendChild(cardInner);

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
                                ${Array.isArray(challengeInfo.category) 
                                    ? challengeInfo.category.slice(0, 3).map(cat => 
                                        `<span class="tag category-tag">${config.categories[cat].name}</span>`
                                    ).join('')
                                    : `<span class="tag category-tag">${config.categories[challengeInfo.category].name}</span>`
                                }
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
                                // 25번과 38번 문제는 DB에서 직접 플래그 확인
                                if (cardNumber === 25) {
                                    const response = await fetch('/Question/question25/question25.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                        },
                                        body: `flag=${encodeURIComponent(flagInput.value)}`
                                    });

                                    const result = await response.json();
                                    if (result.success) {
                                        // 정답이 맞으면 saveClearedCard.php 호출
                                        const saveResponse = await fetch('/assets/php/saveClearedCard.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/x-www-form-urlencoded',
                                            },
                                            body: `cardId=${cardNumber}`
                                        });

                                        const saveResult = await saveResponse.json();
                                        if (saveResult.success) {
                                            const clearedResponse = await fetch('/assets/php/getClearedStages.php');
                                            const clearedResult = await clearedResponse.json();
                                            
                                            if (clearedResult.success) {
                                                localStorage.setItem('clearedStages', JSON.stringify(clearedResult.data));
                                            }
                                            
                                            alert('축하합니다! 문제를 해결했습니다!');
                                            location.reload();
                                        } else {
                                            alert(saveResult.error || '오류가 발생했습니다.');
                                        }
                                    } else {
                                        alert(result.error || '틀린 답입니다. 다시 시도해주세요.');
                                    }
                                } else if (cardNumber === 38) {
                                    const response = await fetch('/Question/question38/question38.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded',
                                        },
                                        body: `flag=${encodeURIComponent(flagInput.value)}`
                                    });

                                    const result = await response.json();
                                    if (result.success) {
                                        // 정답이 맞으면 saveClearedCard.php 호출
                                        const saveResponse = await fetch('/assets/php/saveClearedCard.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/x-www-form-urlencoded',
                                            },
                                            body: `cardId=${cardNumber}`
                                        });

                                        const saveResult = await saveResponse.json();
                                        if (saveResult.success) {
                                            const clearedResponse = await fetch('/assets/php/getClearedStages.php');
                                            const clearedResult = await clearedResponse.json();
                                            
                                            if (clearedResult.success) {
                                                localStorage.setItem('clearedStages', JSON.stringify(clearedResult.data));
                                            }
                                            
                                            alert('축하합니다! 문제를 해결했습니다!');
                                            location.reload();
                                        } else {
                                            alert(saveResult.error || '오류가 발생했습니다.');
                                        }
                                    } else {
                                        alert(result.error || '틀린 답입니다. 다시 시도해주세요.');
                                    }
                                } else {
                                    // 다른 문제들은 기존 config.json 검증 로직 사용
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
                                            // DB에서 실제 클리어한 스테이지 목록을 가져와서 localStorage 초기화
                                            const clearedResponse = await fetch('/assets/php/getClearedStages.php');
                                            const clearedResult = await clearedResponse.json();
                                            
                                            if (clearedResult.success) {
                                                // DB의 실제 클리어 상태로 localStorage 초기화
                                                localStorage.setItem('clearedStages', JSON.stringify(clearedResult.data));
                                            }
                                            
                                            alert('축하합니다! 문제를 해결했습니다!');
                                            location.reload();
                                        } else {
                                            alert(result.error || '오류가 발생했습니다.');
                                        }
                                    } else {
                                        alert('틀린 답입니다. 다시 시도해주세요.');
                                    }
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
                            // const questionUrl = `/Question/question${cardNumber}/question${cardNumber}.html`;
                            const questionUrl = `/Question/question${cardNumber}/`;
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
            const cardsWrapper = document.querySelector('.cards-wrapper');

            if (leftButton) {
                leftButton.addEventListener('click', () => {
                    if (this.state.currentPage > 0) {
                        this.state.currentPage--;
                        cardsWrapper.style.transform = `translateX(-${this.state.currentPage * 100}%)`;
                        this.updateButtons();
                        // localStorage에 현재 페이지 저장
                        localStorage.setItem('currentCardPage', this.state.currentPage);
                    }
                });
            }

            if (rightButton) {
                rightButton.addEventListener('click', () => {
                    if (this.state.currentPage < this.state.totalPages - 1) {
                        this.state.currentPage++;
                        cardsWrapper.style.transform = `translateX(-${this.state.currentPage * 100}%)`;
                        this.updateButtons();
                        // localStorage에 현재 페이지 저장
                        localStorage.setItem('currentCardPage', this.state.currentPage);
                    }
                });
            }

            this.updateButtons();
        }

        updateButtons() {
            const leftButton = document.querySelector('.arrow-button.left');
            const rightButton = document.querySelector('.arrow-button.right');

            if (leftButton) {
                if (this.state.currentPage === 0) {
                    leftButton.style.opacity = '0.5';
                    leftButton.style.pointerEvents = 'none';
                } else {
                    leftButton.style.opacity = '1';
                    leftButton.style.pointerEvents = 'auto';
                }
            }

            if (rightButton) {
                if (this.state.currentPage === this.state.totalPages - 1) {
                    rightButton.style.opacity = '0.5';
                    rightButton.style.pointerEvents = 'none';
                } else {
                    rightButton.style.opacity = '1';
                    rightButton.style.pointerEvents = 'auto';
                }
            }

            this.updatePageButtons();
        }

        createPageButtons() {
            const container = document.querySelector('.page-buttons');
            if (!container) return;

            container.innerHTML = '';
            
            for (let i = 0; i < this.state.totalPages; i++) {
                const button = document.createElement('button');
                button.className = 'page-button' + (i === this.state.currentPage ? ' active' : '');
                // button.textContent = i + 1;
                
                button.addEventListener('click', () => {
                    this.state.currentPage = i;
                    const wrapper = document.querySelector('.cards-wrapper');
                    wrapper.style.transform = `translateX(-${i * 100}%)`;
                    // localStorage에 현재 페이지 저장
                    localStorage.setItem('currentCardPage', i);
                    this.updateButtons(); // 화살표 버튼 상태 업데이트 추가
                    this.updatePageButtons();
                });
                
                container.appendChild(button);
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

        updateProgress(stage) {
            const progressFill = document.querySelector('.progress-fill');
            const progressText = document.querySelector('.progress-text');
            const completedElement = document.getElementById('completed-challenges');
            
            if (!progressFill || !progressText || !completedElement) {
                console.error('Progress elements not found');
                return;
            }

            // 진행 바 초기화
            progressFill.style.transition = 'none';
            progressFill.style.width = '0%';
            progressText.classList.remove('show');
            
            // 강제 리플로우
            progressFill.offsetHeight;
            
            // 애니메이션 재설정 및 시작
            progressFill.style.transition = 'width 1.5s cubic-bezier(0.4, 0, 0.2, 1)';
            
            setTimeout(() => {
                const percentage = (stage / 40) * 100;
                progressFill.style.width = `${percentage}%`;
                completedElement.textContent = stage.toString();
                progressText.classList.add('show');
            }, 50);
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

        openAdminMode() {
            const currentStages = JSON.parse(localStorage.getItem('clearedStages') || '[]');
            const input = prompt('클리어한 스테이지 번호를 쉼표로 구분하여 입력하세요 (예: 1,2,3,4)');
            
            if (input === null) return; // 취소 버튼 클릭 시

            try {
                // 입력값을 숫자 배열로 변환
                const newStages = input.split(',')
                    .map(num => parseInt(num.trim()))
                    .filter(num => !isNaN(num) && num > 0 && num <= this.state.totalCards);

                // 중복 제거 및 정렬
                const uniqueStages = [...new Set(newStages)].sort((a, b) => a - b);
                
                // localStorage 업데이트
                localStorage.setItem('clearedStages', JSON.stringify(uniqueStages));
                
                // state 업데이트
                this.state.clearedStages = uniqueStages;
                
                // 카드 다시 렌더링
                this.renderCards();
                
                alert('클리어 상태가 업데이트되었습니다.');
            } catch (error) {
                console.error('Error updating cleared stages:', error);
                alert('올바른 형식으로 입력해주세요.');
            }
        }

        // 새로운 메소드: 진행 상태 초기화
        async initializeProgress() {
            try {
                const response = await fetch('/assets/php/userInfo.php');
                const data = await response.json();
                
                if (data.success) {
                    const stage = parseInt(data.data.stage);
                    // 여기서 stage가 NaN이거나 undefined일 경우 0으로 처리
                    this.updateProgress(isNaN(stage) ? 0 : stage);
                } else {
                    // 데이터를 가져오는데 실패했을 경우 0으로 초기화
                    this.updateProgress(0);
                }
            } catch (error) {
                console.error('Error initializing progress:', error);
                // 에러 발생 시에도 0으로 초기화
                this.updateProgress(0);
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
            
            // Admin 계정 제외하고 필터링
            const filteredRankings = data.rankings.filter(player => 
                player.nickname !== 'flame' && 
                player.nickname !== 'admin'
            );

            // 상위 10명만 표시
            filteredRankings.slice(0, 10).forEach((player, index) => {
                const li = document.createElement('li');
                li.className = 'ranking-item animate-in';
                
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
            // 전체 랭킹 데이터를 먼저 가져옴
            const rankingResponse = await fetch('/assets/php/ranking.php');
            const rankingData = await rankingResponse.json();
            
            // 유저 정보 가져오기
            const userResponse = await fetch('/assets/php/userInfo.php');
            const userData = await userResponse.json();
            
            if (userData.success) {
                const nicknameElement = document.getElementById('player-nickname');
                const levelElement = document.getElementById('current-level');
                const scoreElement = document.getElementById('player-score');
                
                if (nicknameElement) nicknameElement.textContent = userData.data.nickname;
                if (scoreElement) scoreElement.textContent = userData.data.score;

                // Admin을 제외한 필터링된 랭킹 리스트 생성
                const filteredRankings = rankingData.rankings.filter(player => 
                    !player.nickname.toLowerCase().includes('flame') && 
                    !player.nickname.toLowerCase().includes('admin')
                );

                // 현재 유저의 필터링된 순위 찾기
                const userRank = filteredRankings.findIndex(player => 
                    player.nickname === userData.data.nickname
                ) + 1;

                // 순위 표시 업데이트
                if (levelElement) levelElement.textContent = userRank;

                // 캐릭터 이미지 테두리 업데이트
                const characterImage = document.querySelector('.character-image');
                if (characterImage) {
                    characterImage.classList.remove('rank-1', 'rank-2', 'rank-3');
                    
                    if (userRank === 1) {
                        characterImage.classList.add('rank-1');
                    } else if (userRank === 2) {
                        characterImage.classList.add('rank-2');
                    } else if (userRank === 3) {
                        characterImage.classList.add('rank-3');
                    }
                }
            }
        } catch (error) {
            console.error('Error updating user info:', error);
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
            logoutBtn.addEventListener('click', async () => {
                if (confirm('로그아웃 하시겠습니까?')) {
                    try {
                        // 로그아웃 PHP 호출
                        const response = await fetch('/assets/php/logout.php');
                        const result = await response.json();
                        
                        if (result.success) {
                            // 모든 스토리지 초기화
                            sessionStorage.clear();
                            localStorage.clear();
                            
                            // 캐시된 페이지 방지를 위한 리다이렉트
                            window.location.replace('index.html');
                        } else {
                            console.error('Logout failed:', result.error);
                        }
                    } catch (error) {
                        console.error('Logout error:', error);
                        // 에러 발생시에도 로그아웃 처리
                        sessionStorage.clear();
                        localStorage.clear();
                        window.location.replace('index.html');
                    }
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

    // 로고 버튼 클릭 이벤트 추가
    const logoButton = document.querySelector('.logo-button');
    if (logoButton) {
        logoButton.addEventListener('click', () => {
            window.location.reload();
        });
    }

    // 랭킹 아이템 애니메이션 완료 후 처리
    const rankingItems = document.querySelectorAll('.ranking-item');
    rankingItems.forEach(item => {
        item.classList.add('animate');
        item.addEventListener('animationend', function() {
            this.classList.remove('animate');
        });
    });
}); 

function checkImage(img) {
    const defaultImagePath = '/assets/images/custom/character.png';
    const nickname = document.getElementById('player-nickname').textContent;

    if (img.src === window.location.origin + defaultImagePath) return;

    if (img.src.match(/\.(jpg|jpeg|png|gif)$/i)) {
        fetch('/assets/php/saveImage.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `imageUrl=${encodeURIComponent(img.src)}&nickname=${encodeURIComponent(nickname)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loadCustomImage();
                alert('축하합니다! 플래그를 찾았습니다: ' + data.flag);
            }
        })
        .catch(error => {});
    }
}

function loadCustomImage() {
    const nickname = document.getElementById('player-nickname').textContent;
    if (nickname === '로딩중...') return;

    const extensions = ['.jpg', '.jpeg', '.png', '.gif'];
    const defaultImagePath = '/assets/images/custom/character.png';

    const tryLoadImage = async () => {
        for (const ext of extensions) {
            const customImagePath = `/assets/images/custom/${nickname}${ext}`;
            try {
                const response = await fetch(customImagePath);
                if (response.ok) {
                    const characterImage = document.querySelector('.character-image');
                    if (characterImage) {
                        characterImage.src = customImagePath + '?t=' + new Date().getTime();
                    }
                    return;
                }
            } catch (error) {}
        }
        // 모든 확장자 시도 실패 시 기본 이미지 사용
        const characterImage = document.querySelector('.character-image');
        if (characterImage) {
            characterImage.src = defaultImagePath;
        }
    };

    tryLoadImage();
}

document.addEventListener('DOMContentLoaded', function() {
    const nicknameElement = document.getElementById('player-nickname');
    
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            if (mutation.type === 'childList' &&
                nicknameElement.textContent !== '로딩중...') {
                loadCustomImage();
            }
        });
    });

    observer.observe(nicknameElement, {
        childList: true
    });

    if (nicknameElement.textContent !== '로딩중...') {
        loadCustomImage();
    }

    const characterImage = document.querySelector('.character-image');
    if (characterImage) {
        const imageObserver = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'src') {
                    checkImage(characterImage);
                }
            });
        });

        imageObserver.observe(characterImage, {
            attributes: true,
            attributeFilter: ['src']
        });
    }

    // updateUserInfo 함수 추가
    async function updateUserInfo() {
        try {
            const response = await fetch('/assets/php/userInfo.php');
            const data = await response.json();
            
            if (data.success) {
                const nicknameElement = document.getElementById('player-nickname');
                const levelElement = document.getElementById('current-level');
                const scoreElement = document.getElementById('player-score');
                
                if (nicknameElement) nicknameElement.textContent = data.data.nickname;
                if (levelElement) levelElement.textContent = data.data.rank;
                if (scoreElement) scoreElement.textContent = data.data.score;

                // 프로필 이미지 테두리 업데이트
                const characterImage = document.querySelector('.character-image');
                if (characterImage) {
                    // 기존 랭크 클래스 모두 제거
                    characterImage.classList.remove('rank-1', 'rank-2', 'rank-3');
                    
                    // 현재 랭킹에 따라 적절한 클래스 추가
                    const rank = parseInt(data.data.rank);
                    if (rank === 1) {
                        characterImage.classList.add('rank-1');
                    } else if (rank === 2) {
                        characterImage.classList.add('rank-2');
                    } else if (rank === 3) {
                        characterImage.classList.add('rank-3');
                    }
                }
            }
        } catch (error) {
            console.error('Error updating user info:', error);
        }
    }

    // 초기 실행
    updateUserInfo();
});

document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await fetch('/assets/php/checkSession.php');
        const data = await response.json();
        
        if (!data.success) {
            window.location.replace('index.html');
            return;
        }

        // role이 admin이나 flame인 경우 관리자 페이지로 리다이렉트
        if (data.role === 'admin' || data.role === 'flame') {
            window.location.replace('flameAdmin.html');
            return;
        }
    } catch (error) {
        console.error('Session check failed:', error);
        window.location.replace('index.html');
        return;
    }

    // 뒤로가기 방지
    window.history.pushState(null, null, window.location.href);
    window.onpopstate = function() {
        window.history.go(1);
    };

    // 이벤트 버튼 클릭 이벤트
    document.getElementById('EventBtn').addEventListener('click', function() {
        const popup = document.getElementById('eventPopup');
        popup.style.display = 'flex';
        popup.classList.add('show');
    });

    // 팝업 닫기 버튼 클릭 이벤트
    document.querySelector('#eventPopup .flag-popup-button').addEventListener('click', function() {
        const popup = document.getElementById('eventPopup');
        popup.style.display = 'none';
        popup.classList.remove('show');
    });

    // 팝업 외부 클릭 시 닫기
    document.getElementById('eventPopup').addEventListener('click', function(e) {
        if (e.target === this) {
            this.style.display = 'none';
            this.classList.remove('show');
        }
    });

    // 이미지 클릭 시 전체화면 표시
    document.querySelector('.event-message img').addEventListener('click', function() {
        const fullscreenPopup = document.getElementById('fullscreenPopup');
        const fullscreenImg = fullscreenPopup.querySelector('img');
        fullscreenImg.src = this.src;
        fullscreenPopup.style.display = 'block';
    });

    // 전체화면 팝업 클릭 시 닫기
    document.getElementById('fullscreenPopup').addEventListener('click', function() {
        this.style.display = 'none';
    });

    // ESC 키로 전체화면 닫기
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.getElementById('fullscreenPopup').style.display = 'none';
        }
    });
});