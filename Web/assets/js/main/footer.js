class Footer {
    constructor() {
        this.init();
    }

    init() {
        this.createFooter();
        this.setupEventListeners();
    }

    createFooter() {
        const footerHTML = `
            <footer class="footer">
                <div class="footer-content">
                    <div class="footer-section">
                        <h4>게임 정보</h4>
                        <p>Flame War Game은 보안 학습을 위한 게임입니다.</p>
                        <p>버전: 1.0.0</p>
                    </div>
                    <div class="footer-section">
                        <h4>연락처</h4>
                        <p>이메일: support@flamewargame.com</p>
                        <p>깃허브: <a href="https://github.com/your-repo" target="_blank">GitHub Repository</a></p>
                    </div>
                </div>
            </footer>
            <button class="footer-toggle">
                <img src="assets/images/icons/qna.png" alt="정보">
            </button>
        `;

        document.body.insertAdjacentHTML('beforeend', footerHTML);
    }

    setupEventListeners() {
        const footerToggle = document.querySelector('.footer-toggle');
        const footer = document.querySelector('.footer');

        footerToggle.addEventListener('click', () => {
            footer.classList.toggle('active');
            footerToggle.classList.toggle('active');
        });

        // ESC 키로 푸터 닫기
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && footer.classList.contains('active')) {
                footer.classList.remove('active');
                footerToggle.classList.remove('active');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new Footer();
});

export default Footer; 