window.addEventListener('DOMContentLoaded', async () => {
    try {
        // UIManager 초기화
        const uiManager = new UIManager(); // UIManager 인스턴스 생성

        // GameManager 초기화
        const gameManager = new GameManager();
        await gameManager.initialize();

        // CardManager 초기화 전에 클리어된 카드 정보를 먼저 가져옴
        try {
            const response = await fetch('./assets/php/fetchClearedCards.php');
            const data = await response.json();
            
            if (data.success) {
                gameManager.initializeClearedCards(data.clearedCards);
            } else {
                console.error('Failed to fetch cleared cards:', data.error);
            }
        } catch (error) {
            console.error('Error fetching cleared cards:', error);
        }

        // CardManager 초기화 (클리어된 카드 정보가 로드된 후)
        const cardManager = new CardManager(gameManager, uiManager);

        // 전역 객체로 할당
        window.uiManager = uiManager;
        window.gameManager = gameManager;
        window.cardManager = cardManager;

        // 유저 정보 로드 및 UI 반영
        await uiManager.fetchUserInfo();

        // 로그아웃 버튼 이벤트 핸들러
        document.getElementById('logoutBtn').addEventListener('click', async function() {
            try {
                const response = await fetch('assets/php/logout.php');
                const data = await response.json();
                
                if (data.success) {
                    sessionStorage.clear();
                    localStorage.clear();
                    window.location.replace('login.html');
                } else {
                    console.error('Logout failed');
                }
            } catch (error) {
                console.error('Logout error:', error);
            }
        });

    } catch (error) {
        console.error('초기화 중 오류 발생:', error);
    }
});
