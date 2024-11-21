document.addEventListener('DOMContentLoaded', function() {
    class AdminCardManager {
        constructor() {
            this.currentPage = 0;
            this.cardsPerPage = 10;
            this.totalCards = 40;
            this.totalPages = Math.ceil(this.totalCards / this.cardsPerPage);
            this.init();
            this.setupArrowButtons();
            this.createPageButtons();
        }

        init() {
            const cardsWrapper = document.querySelector('.cards-wrapper');
            if (!cardsWrapper) return;
            
            cardsWrapper.innerHTML = '';

            // 클리어한 스테이지 정보를 가져옵니다
            fetch('assets/php/get_cleared_stages.php')
                .then(response => response.json())
                .then(response => {
                    if (!response.success) {
                        throw new Error(response.error);
                    }
                    
                    const clearedStages = response.data;
                    
                    for (let page = 0; page < this.totalPages; page++) {
                        const grid = document.createElement('div');
                        grid.className = 'cards-grid';
                        
                        const startCard = page * this.cardsPerPage + 1;
                        const endCard = Math.min((page + 1) * this.cardsPerPage, this.totalCards);
                        
                        for (let i = startCard; i <= endCard; i++) {
                            const card = document.createElement('div');
                            card.className = 'challenge-card';
                            
                            const img = document.createElement('img');
                            if (clearedStages.includes(i)) {
                                img.src = `assets/images/monsters/monster_image${i}.png`;
                                img.alt = `Monster ${i}`;
                            } else {
                                img.src = 'assets/images/card_back.jpg';
                                img.alt = 'Card Back';
                            }
                            
                            card.appendChild(img);
                            grid.appendChild(card);
                        }
                        
                        cardsWrapper.appendChild(grid);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        setupArrowButtons() {
            const leftButton = document.querySelector('.arrow-button.left');
            const rightButton = document.querySelector('.arrow-button.right');
            const cardsWrapper = document.querySelector('.cards-wrapper');

            if (leftButton) {
                leftButton.addEventListener('click', () => {
                    if (this.currentPage > 0) {
                        this.currentPage--;
                        cardsWrapper.style.transform = `translateX(-${this.currentPage * 100}%)`;
                        this.updateButtons();
                    }
                });
            }

            if (rightButton) {
                rightButton.addEventListener('click', () => {
                    if (this.currentPage < this.totalPages - 1) {
                        this.currentPage++;
                        cardsWrapper.style.transform = `translateX(-${this.currentPage * 100}%)`;
                        this.updateButtons();
                    }
                });
            }

            this.updateButtons();
        }

        updateButtons() {
            const leftButton = document.querySelector('.arrow-button.left');
            const rightButton = document.querySelector('.arrow-button.right');

            if (leftButton) {
                leftButton.style.opacity = this.currentPage === 0 ? '0.5' : '1';
                leftButton.style.pointerEvents = this.currentPage === 0 ? 'none' : 'auto';
            }

            if (rightButton) {
                rightButton.style.opacity = this.currentPage === this.totalPages - 1 ? '0.5' : '1';
                rightButton.style.pointerEvents = this.currentPage === this.totalPages - 1 ? 'none' : 'auto';
            }

            this.updatePageButtons();
        }

        createPageButtons() {
            const container = document.querySelector('.page-buttons');
            if (!container) return;

            container.innerHTML = '';
            
            for (let i = 0; i < this.totalPages; i++) {
                const button = document.createElement('button');
                button.className = 'page-button' + (i === this.currentPage ? ' active' : '');
                button.textContent = i + 1;
                
                button.addEventListener('click', () => {
                    this.currentPage = i;
                    const wrapper = document.querySelector('.cards-wrapper');
                    wrapper.style.transform = `translateX(-${i * 100}%)`;
                    this.updateButtons();
                });
                
                container.appendChild(button);
            }
        }

        updatePageButtons() {
            const buttons = document.querySelectorAll('.page-button');
            buttons.forEach((button, index) => {
                button.classList.toggle('active', index === this.currentPage);
            });
        }
    }

    async function updateRanking() {
        try {
            const response = await fetch('assets/php/ranking.php');
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
            const response = await fetch('assets/php/user_info.php');
            const data = await response.json();
            
            if (!data.success) {
                console.error('Failed to fetch user info:', data.error);
                return;
            }

            // Update sidebar elements with user info
            document.getElementById('player-nickname').textContent = data.data.nickname;
            document.getElementById('current-level').textContent = data.data.rank;
            document.getElementById('player-score').textContent = data.data.score;
            
            // Update progress bar
            const progress = document.getElementById('progress');
            const completed = data.data.stage;
            const total = 40; // 총 스테이지 수
            
            progress.style.setProperty('--completed', completed);
            progress.style.setProperty('--total', total);
            
            document.getElementById('completed-challenges').textContent = completed;
            document.getElementById('total-challenges').textContent = total;

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
            eventBtn.addEventListener('click', () => eventPopup.style.display = 'flex');
        }
        if (closeEvent) {
            closeEvent.addEventListener('click', () => eventPopup.style.display = 'none');
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

    // 진행 상황 업데이트 예시
    function updateProgress(stage) {
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

        // stage가 유효한 숫자인지 확인
        const validStage = (!isNaN(stage) && stage !== null && stage !== '') ? parseInt(stage) : 0;
        
        const totalStages = 40;
        completedElement.textContent = validStage || '-';  // 유효한 값이 없으면 '-' 표시
        progressFill.style.width = `${(validStage / totalStages) * 100}%`;  // 유효하지 않으면 0%
    }

    // DOM이 로드된 후 실행되도록 보장
    document.addEventListener('DOMContentLoaded', () => {
        // 초기 진행상황 업데이트
        updateProgress(0); // 또는 현재 스테이지 값
    });

    // 초기화
    const adminCardManager = new AdminCardManager();
    setupPopups();
    updateUserInfo();
    updateRanking();
    setInterval(updateRanking, 180000);

    // user_info.php에서 데이터를 가져와서 진행 상황 업데이트
    fetch('assets/php/user_info.php')
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