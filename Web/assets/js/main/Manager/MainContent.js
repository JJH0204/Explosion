class MainContent {
    constructor() {
        this.currentPage = 0;
        this.config = null;
        this.challenges = [];
        this.gridContainer = document.getElementById('challengeGrid');
        
        this.cardStyles = `
            .page {
                display: grid;
                grid-template-columns: repeat(var(--grid-columns), 1fr);
                grid-template-rows: repeat(var(--grid-rows), 1fr);
                gap: 25px;
                padding: 25px;
                position: absolute;
                width: 100%;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease;
            }

            .page.active {
                opacity: 1;
                visibility: visible;
                position: relative;
            }

            .challenge-card {
                position: relative;
                aspect-ratio: 1/1.4;
                cursor: pointer;
                perspective: 1000px;
                transition: transform 0.3s ease;
            }

            .card-inner {
                position: absolute;
                width: 100%;
                height: 100%;
                transform-style: preserve-3d;
                transition: transform 0.6s;
            }

            .card-front, .card-back {
                position: absolute;
                width: 100%;
                height: 100%;
                backface-visibility: hidden;
                border-radius: 15px;
                overflow: hidden;
                padding: 15px;
            }

            .card-front {
                background: var(--primary-color);
                border: 2px solid var(--accent-color);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }

            .card-back {
                background: var(--secondary-color);
                transform: rotateY(180deg);
            }

            .challenge-title {
                font-size: 1.2em;
                margin-bottom: 10px;
                text-align: center;
                color: var(--text-color);
            }

            .challenge-description {
                font-size: 0.9em;
                color: var(--text-color);
                opacity: 0.8;
            }

            .challenge-info {
                position: absolute;
                bottom: 15px;
                left: 15px;
                right: 15px;
                display: flex;
                justify-content: space-between;
                color: var(--text-color);
            }

            .challenge-card:hover {
                transform: translateY(-10px);
            }

            .challenge-card.solved .card-inner {
                transform: rotateY(180deg);
            }
        `;
    }

    async init() {
        await this.loadConfig();
        this.setGridVariables();
        this.addStyles();
        this.renderChallenges();
        this.setupArrowNavigation();
    }

    async loadConfig() {
        try {
            const response = await fetch('/assets/data/config.json');
            this.config = await response.json();
            this.challenges = this.config.challenges;
        } catch (error) {
            console.error('설정 로딩 실패:', error);
        }
    }

    setGridVariables() {
        document.documentElement.style.setProperty('--grid-columns', this.config.game.gridColumns);
        document.documentElement.style.setProperty('--grid-rows', this.config.game.gridRows);
    }

    addStyles() {
        const styleSheet = document.createElement('style');
        styleSheet.textContent = this.cardStyles;
        document.head.appendChild(styleSheet);
    }

    renderChallenges() {
        this.gridContainer.innerHTML = '';
        const totalPages = this.config.game.totalPages;
        const cardsPerPage = this.config.game.cardsPerPage;

        for (let i = 0; i < totalPages; i++) {
            const pageElement = document.createElement('div');
            pageElement.className = `page ${i === 0 ? 'active' : ''}`;
            
            const startIdx = i * cardsPerPage;
            const endIdx = Math.min(startIdx + cardsPerPage, this.challenges.length);

            for (let j = startIdx; j < endIdx; j++) {
                const challenge = this.challenges[j];
                pageElement.appendChild(this.createChallengeCard(challenge));
            }

            this.gridContainer.appendChild(pageElement);
        }
    }

    createChallengeCard(challenge) {
        const card = document.createElement('div');
        card.className = `challenge-card ${challenge.solved ? 'solved' : ''}`;
        
        const difficulty = this.getDifficultyInfo(challenge.id);
        
        card.innerHTML = `
            <div class="card-inner">
                <div class="card-front">
                    <h3 class="challenge-title">${challenge.title}</h3>
                    <p class="challenge-description">${challenge.description}</p>
                </div>
                <div class="card-back">
                    <h3 class="challenge-title">문제 정보</h3>
                    <p class="challenge-description">
                        난이도: ${difficulty.name}<br>
                        점수: ${difficulty.points}점
                    </p>
                    <div class="challenge-info">
                        <span>ID: ${challenge.id}</span>
                    </div>
                </div>
            </div>
        `;

        card.addEventListener('click', () => this.handleCardClick(challenge));
        return card;
    }

    getDifficultyInfo(id) {
        if (id <= 10) return this.config.difficulty.beginner;
        if (id <= 20) return this.config.difficulty.intermediate;
        return this.config.difficulty.advanced;
    }

    handleCardClick(challenge) {
        if (!challenge.solved) {
            window.location.href = challenge.link;
        }
    }

    setupArrowNavigation() {
        const leftArrow = document.querySelector('.arrow-button.left');
        const rightArrow = document.querySelector('.arrow-button.right');
        const pages = document.querySelectorAll('.page');

        if (leftArrow && rightArrow) {
            leftArrow.addEventListener('click', () => this.navigatePage('prev', pages));
            rightArrow.addEventListener('click', () => this.navigatePage('next', pages));
            this.updateArrowState(pages);
        }
    }

    navigatePage(direction, pages) {
        pages[this.currentPage].classList.remove('active');
        
        if (direction === 'prev' && this.currentPage > 0) {
            this.currentPage--;
        } else if (direction === 'next' && this.currentPage < pages.length - 1) {
            this.currentPage++;
        }

        pages[this.currentPage].classList.add('active');
        this.updateArrowState(pages);
    }

    updateArrowState(pages) {
        const leftArrow = document.querySelector('.arrow-button.left');
        const rightArrow = document.querySelector('.arrow-button.right');

        if (leftArrow && rightArrow) {
            leftArrow.style.display = this.currentPage === 0 ? 'none' : 'block';
            rightArrow.style.display = this.currentPage === pages.length - 1 ? 'none' : 'block';
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const mainContent = new MainContent();
    mainContent.init();
});

export default MainContent; 