window.addEventListener('DOMContentLoaded', () => {
    // UI 매니저 먼저 초기화
    const uiManager = new UIManager();
    
    // 게임 매니저 초기화
    const gameManager = new GameManager();
    
    // 카드 매니저 초기화 (UI 매니저와 게임 매니저 참조 전달)
    const cardManager = new CardManager(gameManager, uiManager);
    
    // 전역 객체에 할당 (필요한 경우)
    window.uiManager = uiManager;
    window.gameManager = gameManager;
    window.cardManager = cardManager;
}); 