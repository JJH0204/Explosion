class GameUI {
    constructor() {
        this.currentPage = 0;
        this.cardsPerPage = 8;
        this.totalPages = Math.ceil(40 / this.cardsPerPage);
        this.isAnimating = false;
        
        this.init();
    }

    async init() {
        this.initializeLayout();
        await this.loadCards();
        this.initializeEventListeners();
    }

    initializeLayout() {
        // 사이드바와 메인 컨텐츠 요소 참조
        this.sidebar = document.querySelector('.sidebar');
        this.mainContent = document.querySelector('.main-content');
        
        // 반응형 레이아웃 초기화
        this.handleResponsiveLayout();
        window.addEventListener('resize', () => this.handleResponsiveLayout());
    }

    handleResponsiveLayout() {
        if (!this.sidebar || !this.mainContent) return;
        
        const windowWidth = window.innerWidth;
        
        if (windowWidth >= 1921) {
            this.mainContent.style.transform = 'none';
        } else {
            const offset = windowWidth <= 768 ? '75px' : 
                          windowWidth <= 992 ? '100px' : '125px';
            this.mainContent.style.transform = `translateX(${offset})`;
        }
    }

    async loadCards() {
        try {
            // 클리어된 카드 정보 가져오기
            const clearedResponse = await fetch('assets/php/fetchClearedCards.php');
            const clearedData = await clearedResponse.json();
            
            // 챌린지 데이터 가져오기
            const challengesResponse = await fetch('assets/php/fetchChallenges.php');
            const challengesData = await challengesResponse.json();

            if (!challengesData.success || !clearedData.success) {
                throw new Error('카드 데이터를 불러오는데 실패했습니다.');
            }

            const cards = challengesData.challenges.map(challenge => ({
                id: challenge.id,
                title: challenge.title,
                description: challenge.description,
                isSolved: clearedData.clearedCards.includes(challenge.id.toString())
            }));

            this.renderCards(cards);
            this.updateProgress({
                completed: clearedData.clearedCards.length,
                total: challengesData.challenges.length
            });

        } catch (error) {
            console.error('카드 로딩 실패:', error);
            // 에러 메시지를 사용자에게 표시
            const grid = document.getElementById('challengeGrid');
            if (grid) {
                grid.innerHTML = `
                    <div class="error-message">
                        <p>카드를 불러오는데 실패했습니다.</p>
                        <p>페이지를 새로고침 해주세요.</p>
                    </div>
                `;
            }
        }
    }

    renderCards(cards) {
        const grid = document.getElementById('challengeGrid');
        grid.innerHTML = '';
        
        const wrapper = document.createElement('div');
        wrapper.className = 'pages-wrapper';

        // 페이지별 카드 렌더링
        for (let i = 0; i < this.totalPages; i++) {
            const page = this.createPage(cards, i);
            wrapper.appendChild(page);
        }

        grid.appendChild(wrapper);
        this.showPage(this.currentPage);
    }

    createPage(cards, pageNumber) {
        const page = document.createElement('div');
        page.className = 'page';
        
        const startIdx = pageNumber * this.cardsPerPage;
        const endIdx = Math.min(startIdx + this.cardsPerPage, cards.length);

        for (let i = startIdx; i < endIdx; i++) {
            page.appendChild(this.createCard(cards[i]));
        }

        return page;
    }

    createCard(cardData) {
        const card = document.createElement('div');
        card.className = `card ${cardData.isSolved ? 'solved' : ''}`;
        card.dataset.id = cardData.id;

        card.innerHTML = `
            <div class="card-inner" ${cardData.isSolved ? 'style="transform: rotateY(180deg)"' : ''}>
                <div class="card-front"></div>
                <div class="card-back">
                    ${cardData.isSolved 
                        ? `<img src="/assets/images/monsters/monster_${cardData.id}.png" alt="Monster">` 
                        : `<img src="/assets/images/card_back.jpg" alt="Card Back">`}
                </div>
            </div>
        `;

        card.addEventListener('click', () => this.handleCardClick(cardData.id));
        return card;
    }

    async handleCardClick(cardId) {
        try {
            const response = await fetch(`/php/challenges/${cardId}`);
            const challenge = await response.json();
            
            this.showGamePopup(challenge);
        } catch (error) {
            console.error('챌린지 로딩 실패:', error);
        }
    }

    updateProgress(progress) {
        const completedElement = document.getElementById('completed-challenges');
        const totalElement = document.getElementById('total-challenges');
        
        if (completedElement && totalElement) {
            completedElement.textContent = progress.completed;
            totalElement.textContent = progress.total;
        }
    }

    // ... 나머지 UI 관련 메서드들
} 