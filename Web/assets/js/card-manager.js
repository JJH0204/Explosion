class CardManager {
    constructor() {
        this.currentPage = parseInt(localStorage.getItem('currentCardPage')) || 0;
        this.cardsPerPage = CONFIG.GAME.CARDS_PER_PAGE;
        this.totalPages = CONFIG.GAME.TOTAL_PAGES;
        this.isAnimating = false;
        this.solvedCards = new Set();

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
                this.solvedCards = new Set(data.clearedCards);
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
        card.className = 'card';
        card.dataset.id = number;

        const isSolved = this.solvedCards.has(number.toString());
        if (isSolved) {
            card.classList.add('solved');
        }

        card.innerHTML = `
            <div class="card-inner">
                <div class="card-front">
                    <img src="assets/images/monsters/monster_image${number}.png" alt="Monster ${number}" class="monster-image">
                </div>
                <div class="card-back">
                    <img src="assets/images/card_back.jpg" alt="Card Back" class="card-back-image">
                </div>
            </div>
        `;

        card.addEventListener('click', () => this.handleCardClick(number, isSolved));
        return card;
    }

    async handleCardClick(number, isSolved) {
        try {
            const response = await fetch(`Question/question${number}/question${number}.md`);
            if (!response.ok) throw new Error('Question content not found');
            
            const content = await response.text();
            const popup = document.getElementById('gamePopup');
            const gameContent = document.getElementById('gameContent');
            
            gameContent.innerHTML = marked.parse(content);
            popup.style.display = 'flex';

            popup.addEventListener('click', (e) => {
                if (e.target === popup) {
                    popup.style.display = 'none';
                }
            }, { once: true });
        } catch (error) {
            console.error('Error loading question:', error);
            alert('문제 내용을 불러올 수 없습니다.');
        }
    }

    showPage(pageNumber) {
        const pages = document.querySelectorAll('.page');
        pages.forEach((page, index) => {
            if (index === pageNumber) {
                page.classList.add('active');
                page.style.display = 'grid';
            } else {
                page.classList.remove('active');
                page.style.display = 'none';
            }
        });
        
        this.currentPage = pageNumber;
        localStorage.setItem('currentCardPage', pageNumber);
        this.updateArrowButtons();
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

        this.updateArrowButtons();
    }

    nextPage() {
        if (this.currentPage < this.totalPages - 1) {
            this.showPage(this.currentPage + 1);
        }
    }

    prevPage() {
        if (this.currentPage > 0) {
            this.showPage(this.currentPage - 1);
        }
    }
}