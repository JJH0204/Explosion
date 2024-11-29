class PopupManager {
    constructor() {
        this.popup = document.getElementById('gamePopup');
        this.content = document.getElementById('gameContent');
        this.closeBtn = document.querySelector('.close');
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.hide();
        });

        this.closeBtn.addEventListener('click', () => this.hide());
    }

    show() {
        this.popup.style.display = 'block';
        this.popup.setAttribute('aria-hidden', 'false');
    }

    hide() {
        this.popup.style.display = 'none';
        this.popup.setAttribute('aria-hidden', 'true');
    }

    setContent(content) {
        this.content.innerHTML = content;
    }
} 