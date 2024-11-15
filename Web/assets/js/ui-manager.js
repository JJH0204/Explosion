class UIManager {
    constructor() {
        this.sidebar = document.querySelector('.sidebar');
        this.mainContent = document.querySelector('.main-content');
        this.sidebarToggle = document.querySelector('.sidebar-toggle');
        
        this.initializeSidebar();
        this.initializeResponsiveLayout();
        
        window.addEventListener('resize', () => this.handleResize());
    }

    initializeSidebar() {
        if (this.sidebarToggle && this.sidebar && this.mainContent) {
            this.sidebarToggle.addEventListener('click', () => {
                this.sidebar.classList.toggle('active');
                this.mainContent.classList.toggle('sidebar-active');
                
                requestAnimationFrame(() => {
                    this.adjustLayout();
                });
            });
        }
    }

    initializeResponsiveLayout() {
        this.adjustLayout();
    }

    adjustLayout() {
        if (!this.sidebar || !this.mainContent) return;

        const windowWidth = window.innerWidth;
        
        if (!this.sidebar.classList.contains('active')) {
            this.mainContent.style.transform = 'none';
            return;
        }

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

    isSidebarActive() {
        return this.sidebar?.classList.contains('active') || false;
    }

    getMainContent() {
        return this.mainContent;
    }

    async fetchUserInfo() {
        try {
            const response = await fetch('./assets/php/user_info.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            if (data.success) {
                // 유저 정보를 UI에 반영
                document.querySelector('.character-name').textContent = data.username;
                document.querySelector('.character-level').textContent = `레벨: ${data.level}`;
            } else {
                console.error('Failed to fetch user info:', data.error);
            }
        } catch (error) {
            console.error('Error fetching user info:', error);
        }
    }
} 