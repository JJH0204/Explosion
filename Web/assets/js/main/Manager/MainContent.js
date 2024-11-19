class MainContent {
    constructor() {
        this.state = {
            currentPage: 0,
            config: null,
            challenges: []
        };
        
        this.elements = {
            gridContainer: document.getElementById('challengeGrid'),
            cardsContainer: null,
            paginationContainer: null,
            pages: null,
            popup: null
        };
    }

    async init() {
        try {
            await this.loadConfig();
            this.initializeContainers();
            this.createPopup();
            this.renderContent();
        } catch (error) {
            console.error('초기화 실패:', error);
        }
    }

    async loadConfig() {
        const response = await fetch('/data/config.json');
        this.state.config = await response.json();
        this.state.challenges = this.state.config.challenges;
    }

    initializeContainers() {
        this.cleanupExistingElements();
        this.createCardsContainer();
    }

    cleanupExistingElements() {
        const existingElements = {
            container: document.querySelector('.cards-container'),
            arrows: document.querySelectorAll('.arrow-button'),
            pagination: document.querySelector('.pagination-container')
        };

        existingElements.container?.remove();
        existingElements.arrows.forEach(arrow => arrow.remove());
        existingElements.pagination?.remove();
    }

    createCardsContainer() {
        this.elements.cardsContainer = document.createElement('div');
        this.elements.cardsContainer.className = 'cards-container';
        
        const mainContent = document.querySelector('.main-content');
        mainContent.appendChild(this.elements.cardsContainer);
        this.elements.cardsContainer.appendChild(this.elements.gridContainer);
    }

    renderContent() {
        this.renderChallenges();
        this.setupNavigation();
    }

    renderChallenges() {
        const { totalCards, cardsPerPage } = this.state.config.game;
        const totalPages = Math.ceil(totalCards / cardsPerPage);

        this.elements.gridContainer.innerHTML = '';
        Object.assign(this.elements.gridContainer.style, {
            position: 'relative',
            overflow: 'hidden'
        });

        for (let pageIndex = 0; pageIndex < totalPages; pageIndex++) {
            this.elements.gridContainer.appendChild(
                this.createPage(pageIndex, cardsPerPage)
            );
        }
    }

    createPage(pageIndex, cardsPerPage) {
        const pageElement = this.createPageElement(pageIndex);
        this.addCardsToPage(pageElement, pageIndex, cardsPerPage);
        return pageElement;
    }

    createPageElement(pageIndex) {
        const pageElement = document.createElement('div');
        pageElement.className = `page ${this.getPageState(pageIndex)}`;
        
        const initialTransform = pageIndex > this.state.currentPage ? 
            'translateX(100%)' : 
            pageIndex < this.state.currentPage ? 
                'translateX(-100%)' : 
                'translateX(0)';
        
        pageElement.style.transform = initialTransform;

        return pageElement;
    }

    addCardsToPage(pageElement, pageIndex, cardsPerPage) {
        const startIdx = pageIndex * cardsPerPage;
        const endIdx = Math.min(startIdx + cardsPerPage, this.state.challenges.length);

        for (let i = startIdx; i < endIdx; i++) {
            const challenge = this.state.challenges[i];
            if (challenge) {
                pageElement.appendChild(this.createChallengeCard(challenge));
            }
        }
    }

    createChallengeCard(challenge) {
        const card = document.createElement('div');
        card.className = `challenge-card ${challenge.solved ? 'solved' : ''}`;
        
        const images = {
            monster: `${this.state.config.paths.monstersImages}monster_image${challenge.id}.png`,
            cardBack: `${this.state.config.paths.cardBackImages}card_back.jpg`
        };
        
        card.innerHTML = this.getCardTemplate(challenge.id, images);
        
        const cardFront = card.querySelector('.card-front');
        cardFront.addEventListener('click', (e) => {
            e.stopPropagation();
            this.openPopup(challenge);
        });
        
        card.addEventListener('click', () => this.handleCardClick(challenge));
        return card;
    }

    getCardTemplate(id, images) {
        return `
            <div class="card-front">
                <img src="${images.cardBack}" alt="Monster ${id}" class="card-inner">
            </div>
            <div class="card-back">
                <img src="${images.monster}" alt="Monster ${id}" class="card-inner">
            </div>
        `;
    }

    setupNavigation() {
        this.showArrowButtons();
        this.createPagination();
        this.elements.pages = document.querySelectorAll('.page');
        this.setupArrowNavigation();
    }

    showArrowButtons() {
        const arrows = this.createArrowButtons();
        arrows.forEach(arrow => this.elements.cardsContainer.appendChild(arrow));
    }

    createArrowButtons() {
        return ['◀', '▶'].map((text, i) => {
            const arrow = document.createElement('button');
            arrow.className = `arrow-button ${i === 0 ? 'left' : 'right'}`;
            arrow.textContent = text;
            return arrow;
        });
    }

    createPagination() {
        const { totalCards, cardsPerPage } = this.state.config.game;
        const totalPages = Math.ceil(totalCards / cardsPerPage);

        this.elements.paginationContainer = document.createElement('div');
        this.elements.paginationContainer.className = 'pagination-container';
        
        this.createPaginationButtons(totalPages);
        
        const mainContent = document.querySelector('.main-content');
        mainContent.appendChild(this.elements.paginationContainer);
    }

    createPaginationButtons(totalPages) {
        [...Array(totalPages)].forEach((_, i) => {
            const button = document.createElement('button');
            button.className = `page-button ${i === this.state.currentPage ? 'active' : ''}`;
            button.textContent = i + 1;
            button.addEventListener('click', () => this.jumpToPage(i));
            this.elements.paginationContainer.appendChild(button);
        });
    }

    getPageState(pageIndex) {
        if (pageIndex === this.state.currentPage) return 'active';
        if (pageIndex < this.state.currentPage) return 'previous';
        return 'next';
    }

    handleCardClick(challenge) {
        if (!challenge.solved) {
            window.location.href = challenge.link;
        }
    }

    setupArrowNavigation() {
        const [leftArrow, rightArrow] = document.querySelectorAll('.arrow-button');
        
        if (leftArrow && rightArrow) {
            leftArrow.addEventListener('click', () => this.navigatePage('prev'));
            rightArrow.addEventListener('click', () => this.navigatePage('next'));
            this.updateArrowState();
        }
    }

    navigatePage(direction) {
        const nextPageIndex = direction === 'prev' ? this.state.currentPage - 1 : this.state.currentPage + 1;
        if (nextPageIndex >= 0 && nextPageIndex < this.elements.pages.length) {
            this.jumpToPage(nextPageIndex);
        }
    }

    updateArrowState() {
        const [leftArrow, rightArrow] = document.querySelectorAll('.arrow-button');
        if (leftArrow && rightArrow) {
            leftArrow.style.display = this.state.currentPage === 0 ? 'none' : 'block';
            rightArrow.style.display = this.state.currentPage === this.elements.pages.length - 1 ? 'none' : 'block';
        }
    }

    jumpToPage(pageIndex) {
        if (pageIndex === this.state.currentPage || 
            pageIndex < 0 || 
            pageIndex >= this.elements.pages.length) return;
        
        const direction = pageIndex > this.state.currentPage ? 1 : -1;
        const currentPage = this.elements.pages[this.state.currentPage];
        const targetPage = this.elements.pages[pageIndex];

        // 모든 페이지의 transition 비활성화
        this.elements.pages.forEach(page => {
            page.style.transition = 'none';
            page.style.transform = page === currentPage ? 'translateX(0)' : 
                                  page === targetPage ? `translateX(${100 * direction}%)` : 
                                  'translateX(100%)';
        });

        // 강제 리플로우
        void this.elements.gridContainer.offsetHeight;

        // 현재 페이지와 타겟 페이지만 transition 활성화
        requestAnimationFrame(() => {
            currentPage.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            targetPage.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';

            // 현재 페이지 이동
            currentPage.style.transform = `translateX(${-100 * direction}%)`;
            currentPage.classList.remove('active');
            currentPage.classList.add(direction > 0 ? 'previous' : 'next');

            // 타겟 페이지 이동
            targetPage.style.transform = 'translateX(0)';
            targetPage.classList.remove('previous', 'next');
            targetPage.classList.add('active');

            // 나머지 페이지들 위치 조정
            this.elements.pages.forEach((page, idx) => {
                if (page !== currentPage && page !== targetPage) {
                    page.style.transition = 'none';
                    if (idx < pageIndex) {
                        page.style.transform = 'translateX(-100%)';
                        page.classList.remove('active', 'next');
                        page.classList.add('previous');
                    } else {
                        page.style.transform = 'translateX(100%)';
                        page.classList.remove('active', 'previous');
                        page.classList.add('next');
                    }
                }
            });
        });

        // 페이지네이션 버튼 업데이트
        const pageButtons = this.elements.paginationContainer.querySelectorAll('.page-button');
        pageButtons.forEach((button, index) => {
            button.classList.toggle('active', index === pageIndex);
        });

        // 상태 업데이트
        this.state.currentPage = pageIndex;
        this.updateArrowState();
    }

    createPopup() {
        const popup = document.createElement('div');
        popup.className = 'popup-overlay';
        popup.innerHTML = `
            <div class="popup-content">
                <button class="popup-close">×</button>
                <div class="markdown-content"></div>
            </div>
        `;

        document.body.appendChild(popup);
        
        const closeBtn = popup.querySelector('.popup-close');
        closeBtn.addEventListener('click', () => this.closePopup());
        
        popup.addEventListener('click', (e) => {
            if (e.target === popup) this.closePopup();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.closePopup();
        });

        this.elements.popup = popup;
    }

    async openPopup(challenge) {
        try {
            console.log('Config paths:', this.state.config.paths);
            console.log('Challenge:', challenge);
            
            const markdownPath = `${this.state.config.paths.markdown}${challenge.markdownFile}`;
            console.log('Markdown 파일 경로:', markdownPath);
            
            const response = await fetch(markdownPath);
            console.log('Fetch 응답 상태:', response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`Markdown 파일을 불러올 수 없습니다: ${markdownPath}`);
            }
            
            const markdown = await response.text();
            console.log('마크다운 내용:', markdown); // 마크다운 내용 확인

            // marked 함수 존재 여부 확인
            if (typeof marked === 'undefined') {
                console.error('marked 라이브러리가 로드되지 않았습니다.');
                throw new Error('마크다운 변환기를 찾을 수 없습니다.');
            }

            // marked 옵션 설정
            marked.setOptions({
                breaks: true, // 줄바꿈 활성화
                gfm: true,    // GitHub Flavored Markdown 활성화
                sanitize: false // HTML 태그 허용
            });

            try {
                const html = marked.parse(markdown); // marked.parse 사용
                console.log('변환된 HTML:', html); // 변환된 HTML 확인

                const contentDiv = this.elements.popup.querySelector('.markdown-content');
                contentDiv.innerHTML = html;
                
                // 팝업 제목 추가
                const titleDiv = document.createElement('div');
                titleDiv.className = 'popup-title';
                titleDiv.innerHTML = `
                    <h2>${challenge.title}</h2>
                    <p>${challenge.description}</p>
                `;
                
                contentDiv.insertBefore(titleDiv, contentDiv.firstChild);
                
                requestAnimationFrame(() => {
                    this.elements.popup.classList.add('active');
                });
            } catch (parseError) {
                console.error('마크다운 변환 실패:', parseError);
                throw new Error('마크다운 변환에 실패했습니다.');
            }
        } catch (error) {
            console.error('팝업 열기 실패:', error);
            console.error('에러 상세 정보:', {
                config: this.state.config,
                challenge: challenge,
                markdownPath: `${this.state.config.paths.markdown}${challenge.markdownFile}`
            });
            alert('문제 내용을 불러오는데 실패했습니다.');
        }
    }

    closePopup() {
        if (this.elements.popup) {
            this.elements.popup.classList.remove('active');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => new MainContent().init());

export default MainContent; 