class AdminCardManager {
    constructor() {
        this.currentPage = 0;
        this.cardsPerPage = CONFIG.GAME.CARDS_PER_PAGE;
        this.totalPages = CONFIG.GAME.TOTAL_PAGES;
        this.isAnimating = false;

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
        if (pageNumber === 0) {
            page.classList.add('active');
        }
        
        const startCard = pageNumber * this.cardsPerPage;
        const endCard = Math.min(startCard + this.cardsPerPage, CONFIG.GAME.TOTAL_CARDS);
        
        for (let i = startCard; i < endCard; i++) {
            const card = this.createCard(i + 1);
            page.appendChild(card);
        }
        
        return page;
    }

    createCard(number) {
        const card = document.createElement('div');
        card.className = 'card admin-card';
        card.dataset.id = number;

        card.innerHTML = `
            <div class="card-inner">
                <div class="card-front">
                    <img src="assets/images/monsters/monster_image${number}.png" alt="Monster ${number}" class="monster-image">
                </div>
                <div class="card-back">
                    <img src="assets/images/monsters/monster_image${number}.png" alt="Monster ${number}" class="monster-image">
                </div>
            </div>
        `;

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
        
        const pages = document.querySelectorAll('.page');
        if (!pages.length) return;
        
        this.isAnimating = true;
        
        pages.forEach((page, index) => {
            if (index === pageNumber) {
                page.classList.add('active');
            } else {
                page.classList.remove('active');
            }
        });
        
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