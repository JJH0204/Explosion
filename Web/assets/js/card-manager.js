class CardManager {
    constructor(gameManager, uiManager) {
        this.gameManager = gameManager;
        this.uiManager = uiManager;
        this.currentPage = 0; // 기본값 설정
        this.cardsPerPage = CONFIG.GAME.CARDS_PER_PAGE;
        this.totalPages = CONFIG.GAME.TOTAL_PAGES;
        this.isAnimating = false;

        // DOM이 완전히 로드된 후 초기화
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.initializeCards();
                this.initializeArrowButtons();
            });
        } else {
            this.initializeCards();
            this.initializeArrowButtons();
        }
    }

    async initializeCards() {
        const grid = document.getElementById('challengeGrid');
        if (!grid) {
            console.error('Challenge grid element not found');
            return;
        }

        grid.innerHTML = '';
        
        const wrapper = document.createElement('div');
        wrapper.className = 'pages-wrapper';
        
        try {
            const response = await fetch('./assets/php/fetchClearedCards.php');
            const data = await response.json();
            
            if (data.success) {
                this.gameManager.initializeClearedCards(data.clearedCards);
            }
        } catch (error) {
            console.error('Error fetching cleared cards:', error);
        }
        
        for (let i = 0; i < this.totalPages; i++) {
            const page = this.createPage(i);
            if (page) {
                wrapper.appendChild(page);
            }
        }
        
        grid.appendChild(wrapper);
        this.showPage(this.currentPage);
        this.updateArrowButtons();
    }

    createPage(pageNumber) {
        const page = document.createElement('div');
        page.className = 'page';
        
        const startCard = pageNumber * CONFIG.GAME.CARDS_PER_PAGE;
        const endCard = Math.min(startCard + CONFIG.GAME.CARDS_PER_PAGE, CONFIG.GAME.TOTAL_CARDS);

        for (let i = startCard; i < endCard; i++) {
            const card = this.createCard(i + 1);
            page.appendChild(card);
        }

        return page;
    }

    createCard(number) {
        const card = document.createElement('div');
        card.className = 'card';
        card.dataset.id = number;

        const isSolved = this.gameManager.solvedCards.has(number.toString());
        if (isSolved) {
            card.classList.add('solved');
        }

        card.innerHTML = `
            <div class="card-inner" ${isSolved ? 'style="transform: rotateY(180deg)"' : ''}>
                <div class="card-front"></div>
                <div class="card-back">
                    ${isSolved 
                        ? `<img src="assets/images/monsters/monster_image${number}.png" alt="Monster ${number}" class="monster-image">` 
                        : `<img src="assets/images/card_back.jpg" alt="Default card" class="default-image">`}
                </div>
            </div>
        `;

        card.addEventListener('click', () => {
            if (!card.classList.contains('solved')) {
                this.gameManager.revealGame(card, `game${number}`);
            }
        });

        return card;
    }

    updateArrowButtons() {
        const leftButton = document.querySelector('.arrow-button.left');
        const rightButton = document.querySelector('.arrow-button.right');
        
        if (leftButton) {
            leftButton.disabled = this.currentPage === 0;
            leftButton.style.opacity = this.currentPage === 0 ? '0.5' : '1';
        }
        
        if (rightButton) {
            rightButton.disabled = this.currentPage === this.totalPages - 1;
            rightButton.style.opacity = this.currentPage === this.totalPages - 1 ? '0.5' : '1';
        }
    }

    initializeArrowButtons() {
        const leftArrow = document.querySelector('.arrow-button.left');
        const rightArrow = document.querySelector('.arrow-button.right');

        if (leftArrow) {
            leftArrow.addEventListener('click', () => this.prevPage());
        }
        if (rightArrow) {
            rightArrow.addEventListener('click', () => this.nextPage());
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                this.prevPage();
            } else if (e.key === 'ArrowRight') {
                this.nextPage();
            }
        });

        this.updateArrowButtons();
    }

    showPage(pageNumber) {
        if (this.isAnimating) return;
        
        const wrapper = document.querySelector('.pages-wrapper');
        if (!wrapper) return;
        
        this.isAnimating = true;
        wrapper.style.transform = `translateX(-${pageNumber * 25}%)`;
        
        setTimeout(() => {
            this.isAnimating = false;
        }, 500);
        
        this.currentPage = pageNumber;
        this.updateArrowButtons();
    }

    nextPage() {
        if (this.currentPage < this.totalPages - 1 && !this.isAnimating) {
            this.currentPage++;
            this.showPage(this.currentPage);
        }
    }

    prevPage() {
        if (this.currentPage > 0 && !this.isAnimating) {
            this.currentPage--;
            this.showPage(this.currentPage);
        }
    }
}