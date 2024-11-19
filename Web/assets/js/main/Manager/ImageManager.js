class ImageManager {
    constructor(config) {
        this.config = {
            defaultImagePath: config.defaultImagePath || 'assets/images/custom/character.png',
            customImagePath: config.customImagePath || 'assets/images/custom/',
            imageExtensions: config.imageExtensions || ['jpg', 'jpeg', 'png', 'gif'],
            characterImage: document.getElementById('character-image')
        };
        
        this.currentImage = null;
        this.customImages = [];
    }

    async init() {
        try {
            await this.loadCustomImages();
            await this.setDefaultImage();
            this.setupImageChangeListener();
        } catch (error) {
            console.error('이미지 매니저 초기화 실패:', error);
        }
    }

    async loadCustomImages() {
        const response = await fetch('/assets/images/custom/');
            const images = await response.json();
            this.customImages = images.map(img => ({
            path: `${this.config.customImagePath}${img}`,
            name: img.split('.')[0]
        }));
    }

    async setDefaultImage() {
        if (this.config.characterImage) {
            try {
                // 저장된 이미지 경로 확인
                const savedImage = localStorage.getItem('selectedCharacterImage');
                const imagePath = savedImage || this.config.defaultImagePath;
                
                await this.loadImage(imagePath);
                this.currentImage = imagePath;
            } catch (error) {
                console.error('기본 이미지 설정 실패:', error);
                // 기본 이미지 로드 실패 시 대체 이미지 사용
                this.config.characterImage.src = this.config.defaultImagePath;
            }
        }
    }

    async loadImage(path) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => {
                if (this.config.characterImage) {
                    this.config.characterImage.src = path;
                    localStorage.setItem('selectedCharacterImage', path);
                    resolve();
                }
            };
            img.onerror = () => {
                reject(new Error(`이미지 로드 실패: ${path}`));
            };
            img.src = path;
        });
    }

    setupImageChangeListener() {
        if (this.config.characterImage) {
            this.config.characterImage.addEventListener('click', () => {
                this.showImageSelector();
            });
        }
    }

    showImageSelector() {
        const popup = document.createElement('div');
        popup.className = 'image-selector-popup';
        popup.innerHTML = `
            <div class="image-selector-content">
                <h3>캐릭터 이미지 선택</h3>
                <div class="image-grid">
                    ${this.createImageGrid()}
                </div>
                <button class="close-button">닫기</button>
            </div>
        `;

        document.body.appendChild(popup);

        popup.querySelector('.close-button').addEventListener('click', () => {
            popup.remove();
        });

        const images = popup.querySelectorAll('.image-option');
        images.forEach(img => {
            img.addEventListener('click', async () => {
                try {
                    await this.loadImage(img.src);
                    this.currentImage = img.src;
                    popup.remove();
                } catch (error) {
                    console.error('이미지 변경 실패:', error);
                }
            });
        });
    }

    createImageGrid() {
        return this.customImages.map(img => `
            <img 
                src="${img.path}" 
                alt="${img.name}" 
                class="image-option" 
                title="${img.name}"
            >
        `).join('') + `
            <img 
                src="${this.config.defaultImagePath}" 
                alt="기본 캐릭터" 
                class="image-option" 
                title="기본 캐릭터"
            >
        `;
    }

    getCurrentImage() {
        return this.currentImage;
    }

    async changeImage(path) {
        try {
            await this.loadImage(path);
            this.currentImage = path;
            return true;
        } catch (error) {
            console.error('이미지 변경 실패:', error);
            return false;
        }
    }
}

export { ImageManager }; 