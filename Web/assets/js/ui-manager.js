class UIManager {
    constructor() {
        this.sidebar = document.querySelector('.sidebar');
        this.mainContent = document.querySelector('.main-content');
        this.leftArrowButton = document.querySelector('.arrow-button.left');
        
        this.initializeResponsiveLayout();
        window.addEventListener('resize', () => this.handleResize());
    }

    initializeResponsiveLayout() {
        this.adjustLayout();
    }

    adjustLayout() {
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

    handleResize() {
        this.adjustLayout();
    }

    getMainContent() {
        return this.mainContent;
    }
} 
