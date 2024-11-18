class MarkdownLoader {
    constructor() {
        this.initializeMarked();
    }

    initializeMarked() {
        marked.setOptions({
            breaks: true,
            gfm: true,
            headerIds: true,
            mangle: false
        });
    }

    async loadChallenge(challengeId) {
        try {
            const filePath = `${CONFIG.PATHS.MARKDOWN}${CONFIG.FILE.PREFIX}${challengeId}${CONFIG.FILE.EXTENSION}`;
            const response = await fetch(filePath);
            
            if (!response.ok) {
                throw new Error(`파일을 찾을 수 없습니다: ${filePath}`);
            }
            
            const markdownContent = await response.text();
            return marked.parse(markdownContent);
        } catch (error) {
            console.error('마크다운 로딩 에러:', error);
            return `<p class="error">문제 설명을 불러올 수 없습니다.</p>`;
        }
    }
} 