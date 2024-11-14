class CardManager {
    constructor(gameManager, uiManager) {
        this.gameManager = gameManager;
        this.uiManager = uiManager;
        this.currentPage = 0;
        this.cardsPerPage = 10;
        this.totalPages = Math.ceil(CONFIG.GAME.TOTAL_CARDS / this.cardsPerPage);
        this.isAnimating = false;

        this.initializeCards();
        this.initializeArrowButtons();
        this.preventWheelScroll();
    }

    preventWheelScroll() {
        const grid = document.getElementById('challengeGrid');
        if (grid) {
            grid.addEventListener('wheel', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
                    if (e.deltaY > 0) {
                        this.nextPage();
                    } else {
                        this.prevPage();
                    }
                }
            }, { passive: false });
        }

        document.addEventListener('wheel', (e) => {
            if (e.target.closest('#challengeGrid')) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, { passive: false, capture: true });
    }

    initializeCards() {
        const grid = document.getElementById('challengeGrid');
        grid.innerHTML = '';

        const wrapper = document.createElement('div');
        wrapper.className = 'pages-wrapper';
        
        // 모든 페이지 생성
        for (let i = 0; i < this.totalPages; i++) {
            const page = this.createPage(i);
            wrapper.appendChild(page);
        }

        grid.appendChild(wrapper);
        this.updateArrowButtons();
    }

    createPage(pageNumber) {
        const page = document.createElement('div');
        page.className = 'page';
        
        const startCard = pageNumber * this.cardsPerPage;
        const endCard = Math.min(startCard + this.cardsPerPage, CONFIG.GAME.TOTAL_CARDS);

        for (let i = startCard; i < endCard; i++) {
            const card = this.createCard(i + 1);
            page.appendChild(card);
        }

        return page;
    }

    async nextPage() {
        if (this.currentPage < this.totalPages - 1 && !this.isAnimating) {
            this.isAnimating = true;
            this.currentPage++;
            this.updatePagePosition();
            
            setTimeout(() => {
                this.isAnimating = false;
            }, 500);
            
            this.updateArrowButtons();
        }
    }

    async prevPage() {
        if (this.currentPage > 0 && !this.isAnimating) {
            this.isAnimating = true;
            this.currentPage--;
            this.updatePagePosition();
            
            setTimeout(() => {
                this.isAnimating = false;
            }, 500);
            
            this.updateArrowButtons();
        }
    }

    updatePagePosition() {
        const wrapper = document.querySelector('.pages-wrapper');
        if (wrapper) {
            wrapper.style.transform = `translateX(-${this.currentPage * 100}%)`;
            this.uiManager?.adjustLayout();
        }
    }

    updateArrowButtons() {
        const leftArrow = document.querySelector('.arrow-button.left');
        const rightArrow = document.querySelector('.arrow-button.right');

        if (leftArrow) {
            leftArrow.style.opacity = this.currentPage === 0 ? '0.3' : '1';
            leftArrow.style.pointerEvents = this.currentPage === 0 ? 'none' : 'auto';
        }
        if (rightArrow) {
            rightArrow.style.opacity = 
                this.currentPage === this.totalPages - 1 ? '0.3' : '1';
            rightArrow.style.pointerEvents = 
                this.currentPage === this.totalPages - 1 ? 'none' : 'auto';
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

        // 키보드 이벤트
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                this.prevPage();
            } else if (e.key === 'ArrowRight') {
                this.nextPage();
            }
        });

        this.updateArrowButtons();
    }

    createCard(number) {
        const card = document.createElement('div');
        card.className = 'card';
        card.dataset.id = number;

        card.innerHTML = `
            <div class="card-inner">
                <div class="card-front">
                    <!-- <span class="card-number top-left">${number}</span> -->
                </div>
                <div class="card-back">
                    <img src="assets/images/monster_${number}.png" alt="Monster ${number}" class="monster-image">
                </div>
            </div>
        `;

        // 카드 클릭 이벤트
        card.addEventListener('click', () => {
            if (!card.classList.contains('solved')) {
                this.gameManager.revealGame(card, `game${number}`);
            }
        });

        return card;
    }
} 