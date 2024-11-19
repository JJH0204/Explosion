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
        
        this.setupPopupEventListeners();
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
        this.createArrowButtons();
        this.createPagination();
        this.elements.pages = document.querySelectorAll('.page');
    }

    createArrowButtons() {
        const leftArrow = document.createElement('button');
        leftArrow.className = 'arrow-button left';
        leftArrow.innerHTML = '&#10094;';
        leftArrow.addEventListener('click', () => this.previousPage());

        const rightArrow = document.createElement('button');
        rightArrow.className = 'arrow-button right';
        rightArrow.innerHTML = '&#10095;';
        rightArrow.addEventListener('click', () => this.nextPage());

        // main 요소에 화살표 추가
        const mainElement = document.querySelector('main');
        mainElement.appendChild(leftArrow);
        mainElement.appendChild(rightArrow);

        this.updateArrowVisibility();
    }

    updateArrowVisibility() {
        const leftArrow = document.querySelector('.arrow-button.left');
        const rightArrow = document.querySelector('.arrow-button.right');
        
        if (leftArrow && rightArrow) {
            // 항상 표시하되, 첫/마지막 페이지에서는 투명도 조절
            leftArrow.style.opacity = this.state.currentPage === 0 ? '0.3' : '1';
            leftArrow.style.pointerEvents = this.state.currentPage === 0 ? 'none' : 'auto';
            
            const lastPage = Math.ceil(this.state.challenges.length / this.state.config.game.cardsPerPage) - 1;
            rightArrow.style.opacity = this.state.currentPage === lastPage ? '0.3' : '1';
            rightArrow.style.pointerEvents = this.state.currentPage === lastPage ? 'none' : 'auto';
        }
    }
    createPagination() {
        const totalPages = Math.ceil(this.state.challenges.length / this.state.config.game.cardsPerPage);
        
        // 기존 페이지네이션 제거
        const existingPagination = document.querySelector('.pagination-container');
        if (existingPagination) {
            existingPagination.remove();
        }

        // 새 페이지네이션 생성
        this.elements.paginationContainer = document.createElement('div');
        this.elements.paginationContainer.className = 'pagination-container';

        for (let i = 0; i < totalPages; i++) {
            const button = document.createElement('button');
            button.className = `page-button ${i === this.state.currentPage ? 'active' : ''}`;
            button.textContent = i + 1;
            button.addEventListener('click', () => this.jumpToPage(i));
            this.elements.paginationContainer.appendChild(button);
        }

        // main-content에 페이지네이션 추가
        const mainContent = document.querySelector('.main-content');
        mainContent.appendChild(this.elements.paginationContainer);
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

    previousPage() {
        if (this.state.currentPage > 0) {
            this.jumpToPage(this.state.currentPage - 1);
        }
    }

    nextPage() {
        const totalPages = Math.ceil(this.state.challenges.length / this.state.config.game.cardsPerPage);
        if (this.state.currentPage < totalPages - 1) {
            this.jumpToPage(this.state.currentPage + 1);
        }
    }

    jumpToPage(pageIndex) {
        if (pageIndex === this.state.currentPage || 
            pageIndex < 0 || 
            pageIndex >= Math.ceil(this.state.challenges.length / this.state.config.game.cardsPerPage)) {
            return;
        }

        // 페이지 상태 업데이트
        this.state.currentPage = pageIndex;
        
        // 모든 페이지 업데이트
        this.elements.pages.forEach((page, index) => {
            if (index === pageIndex) {
                page.className = 'page active';
                page.style.transform = 'translateX(0)';
            } else if (index < pageIndex) {
                page.className = 'page previous';
                page.style.transform = 'translateX(-100%)';
            } else {
                page.className = 'page next';
                page.style.transform = 'translateX(100%)';
            }
        });

        // 페이지네이션 버튼 업데이트
        const pageButtons = this.elements.paginationContainer.querySelectorAll('.page-button');
        pageButtons.forEach((button, index) => {
            button.classList.toggle('active', index === pageIndex);
        });

        // 화살표 상태 업데이트
        this.updateArrowVisibility();
    }

    createPopup() {
        this.elements.popup = document.querySelector('.popup-overlay');
        
        if (!this.elements.popup) {
            console.error('팝업 요소를 찾을 수 없습니다.');
            return;
        }

        const closeButton = this.elements.popup.querySelector('.popup-close');
        if (closeButton) {
            closeButton.addEventListener('click', () => this.closePopup());
        }
    }

    setupPopupEventListeners() {
        // 팝업 오버레이 클릭 시 닫기
        document.querySelector('.popup-overlay').addEventListener('click', (e) => {
            if (e.target.classList.contains('popup-overlay')) {
                this.closePopup();
            }
        });

        // ESC 키 누를 때 팝업 닫기
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closePopup();
            }
        });

        // 닫기 버튼 클릭 시 팝업 닫기
        document.querySelector('.popup-close').addEventListener('click', () => {
            this.closePopup();
        });
    }

    async openPopup(challenge) {
        try {
            const markdownPath = `${this.state.config.paths.markdown}${challenge.markdownFile}`;
            const response = await fetch(markdownPath);
            
            if (!response.ok) {
                throw new Error(`마크다운 파일을 불러올 수 없습니다: ${markdownPath}`);
            }
            
            const markdown = await response.text();
            const html = marked.parse(markdown);

            // 헤더 업데이트
            const headerDiv = this.elements.popup.querySelector('.popup-header');
            headerDiv.innerHTML = `
                <h2>${challenge.title}</h2>
                <p>${challenge.description}</p>
            `;

            // 챌린지 정보 업데이트
            const category = this.state.config.categories[challenge.category];
            const difficulty = this.state.config.difficulty[challenge.difficulty];
            
            const infoDiv = this.elements.popup.querySelector('.challenge-info');
            infoDiv.innerHTML = `
                <div class="info-item">
                    <span class="info-label">카테고리</span>
                    <br>
                    <span class="info-value">${category.name}</span>
                    <br>
                    <span class="info-description">${category.description}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">난이도</span>
                    <br>
                    <span class="info-value">${difficulty.name}</span>
                    <br>
                    <span class="info-points">💎 ${difficulty.points} 포인트</span>
                </div>
            `;

            // 마크다운 콘텐츠 업데이트
            const contentDiv = this.elements.popup.querySelector('.markdown-content');
            contentDiv.innerHTML = `
                <div class="content-wrapper">
                    ${html}
                </div>
            `;

            // 문제 페이지로 이동하는 버튼 이벤트 설정
            const gotoBtn = this.elements.popup.querySelector('.goto-challenge-btn');
            if (gotoBtn) {
                gotoBtn.addEventListener('click', () => {
                    window.location.href = challenge.link;
                });
            }

            // 플래그 제출 버튼 이벤트 설정
            const submitBtn = this.elements.popup.querySelector('.submit-btn');
            const flagInput = this.elements.popup.querySelector('.flag-input');
            
            if (submitBtn && flagInput) {
                submitBtn.addEventListener('click', () => {
                    const submittedFlag = flagInput.value.trim();
                    if (submittedFlag === challenge.answer) {
                        alert('정답입니다!');
                        this.closePopup();
                    } else {
                        alert('틀렸습니다. 다시 시도해보세요.');
                    }
                });
            }

            // 스크롤 표시기 추가
            const popupContent = this.elements.popup.querySelector('.popup-content');
            const scrollIndicator = document.createElement('div');
            scrollIndicator.className = 'scroll-indicator';
            popupContent.appendChild(scrollIndicator);

            // 스크롤 이벤트 리스너 추가
            const handleScroll = () => {
                const isScrollable = popupContent.scrollHeight > popupContent.clientHeight;
                const isBottom = popupContent.scrollHeight - popupContent.scrollTop === popupContent.clientHeight;
                
                scrollIndicator.style.display = isScrollable && !isBottom ? 'flex' : 'none';
            };

            popupContent.addEventListener('scroll', handleScroll);
            // 초기 스크롤 상태 확인
            setTimeout(handleScroll, 100);

            // 팝업 표시 전에 이벤트 리스너 재설정
            this.setupPopupEventListeners();

            // 팝업 표시
            requestAnimationFrame(() => {
                document.querySelector('.popup-overlay').classList.add('active');
                const popupContent = document.querySelector('.popup-content');
                if (popupContent) {
                    popupContent.scrollTop = 0;
                }
            });

        } catch (error) {
            console.error('팝업 열기 실패:', error);
            alert('문제 내용을 불러오는데 실패했습니다.');
        }
    }

    // TODO: 정답 처리 로직 구현
    // 진행상황 갱신
    // 점수 갱신
    // 문제 갱신
    // 성공 효과 표시 (비쥬얼 효과)
    setupActionButtons(challenge) {
        const gotoBtn = this.elements.popup.querySelector('.goto-challenge-btn');
        const submitBtn = this.elements.popup.querySelector('.submit-btn');
        
        if (gotoBtn) {
            const newGotoBtn = gotoBtn.cloneNode(true);
            gotoBtn.parentNode.replaceChild(newGotoBtn, gotoBtn);
            newGotoBtn.addEventListener('click', () => {
                window.location.href = challenge.link;
            });
        }

        if (submitBtn) {
            const newSubmitBtn = submitBtn.cloneNode(true);
            submitBtn.parentNode.replaceChild(newSubmitBtn, submitBtn);
            const flagInput = this.elements.popup.querySelector('.flag-input');
            
            newSubmitBtn.addEventListener('click', async () => {
                const submittedFlag = flagInput.value.trim();
                
                try {
                    const formData = new FormData();
                    formData.append('challengeId', challenge.id);
                    formData.append('answer', submittedFlag);

                    const response = await fetch('/assets/php/main/verifyAnswer.php', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();
                    
                    if (result.success) {
                        // 카드 요소 찾기
                        const card = document.querySelector(`[data-challenge-id="${challenge.id}"]`);
                        const cardBack = card.querySelector('.card-back');
                        
                        // 카드 뒤집기 애니메이션 적용
                        card.classList.add('solved');
                        setTimeout(() => {
                            card.classList.add('flipped');
                            cardBack.style.display = 'flex';
                            cardBack.style.opacity = '1';
                        }, 100);

                        // 성공 오버레이 표시
                        const successOverlay = document.createElement('div');
                        successOverlay.className = 'success-overlay';
                        successOverlay.innerHTML = `
                            <div class="success-content">
                                <div class="success-icon">✓</div>
                                <h3>Challenge Completed!</h3>
                                <p>+${result.points} points</p>
                            </div>
                        `;
                        document.body.appendChild(successOverlay);

                        // 점수와 진행 상황 업데이트
                        await this.updateProgress(result.points);

                        // 성공 효과 표시 후 팝업 닫기
                        setTimeout(() => {
                            successOverlay.classList.add('fade-out');
                            setTimeout(() => {
                                successOverlay.remove();
                                this.closePopup();
                            }, 500);
                        }, 2000);
                    } else {
                        throw new Error(result.error);
                    }
                } catch (error) {
                    console.error('정답 처리 실패:', error);
                    this.showError(error.message || '오류가 발생했습니다.');
                }
            });
        }
    }

    closePopup() {
        const popup = document.querySelector('.popup-overlay');
        if (popup) {
            popup.classList.remove('active');
            // 스크롤 표시기 제거
            const scrollIndicator = popup.querySelector('.scroll-indicator');
            if (scrollIndicator) {
                scrollIndicator.remove();
            }
            // 입력 필드 초기화
            const flagInput = popup.querySelector('.flag-input');
            if (flagInput) {
                flagInput.value = '';
            }
        }
    }

    // 진행 상황 업데이트 메서드
    async updateProgress(points) {
        const scoreElement = document.getElementById('player-score');
        const completedElement = document.getElementById('completed-challenges');
        
        if (scoreElement && completedElement) {
            const currentScore = parseInt(scoreElement.textContent);
            const currentCompleted = parseInt(completedElement.textContent);
            
            scoreElement.textContent = currentScore + points;
            completedElement.textContent = currentCompleted + 1;
        }
    }

    // 에러 메시지 표시 메서드
    showError(message) {
        const flagInput = this.elements.popup.querySelector('.flag-input');
        flagInput.classList.add('error');
        
        const errorMessage = document.createElement('div');
        errorMessage.className = 'error-message';
        errorMessage.textContent = message;
        flagInput.parentNode.appendChild(errorMessage);

        setTimeout(() => {
            flagInput.classList.remove('error');
            errorMessage.remove();
        }, 3000);
    }
}

document.addEventListener('DOMContentLoaded', () => new MainContent().init());

export default MainContent; 