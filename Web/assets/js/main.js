window.addEventListener('DOMContentLoaded', async () => {
    try {
        // UI 매니저 초기화
        const uiManager = new UIManager();
        
        // 게임 매니저 초기화
        const gameManager = new GameManager();
        await gameManager.initialize();
        
        // 카드 매니저 초기화
        const cardManager = new CardManager(gameManager, uiManager);
        
        // 전역 객체에 할당
        window.uiManager = uiManager;
        window.gameManager = gameManager;
        window.cardManager = cardManager;

        // 사용자 정보와 랭킹 정보를 가져옵니다
        await Promise.all([
            uiManager.fetchUserInfo(),
            uiManager.fetchRanking()
        ]);
    } catch (error) {
        console.error('초기화 중 오류 발생:', error);
    }
}); 