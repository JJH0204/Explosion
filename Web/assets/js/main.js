window.addEventListener('DOMContentLoaded', async () => {
    try {
        // UIManager 초기화
        const uiManager = new UIManager(); // UIManager 인스턴스 생성

        // GameManager 초기화
        const gameManager = new GameManager();
        await gameManager.initialize();

        // CardManager 초기화
        const cardManager = new CardManager(gameManager, uiManager);

        // 전역 객체로 할당
        window.uiManager = uiManager;
        window.gameManager = gameManager;
        window.cardManager = cardManager;

        // 유저 정보 로드 및 UI 반영
        await uiManager.fetchUserInfo(); // 닉네임 UI 반영

        // 클리어된 카드 정보를 DB에서 가져와 초기화
        const response = await fetch('./assets/php/fetchClearedCards.php');
        const data = await response.json();

        if (data.success) {
            gameManager.initializeClearedCards(data.clearedCards);
        } else {
            console.error('Failed to fetch cleared cards:', data.error);
        }

        // 로그아웃 버튼 이벤트 리스너 추가
        document.getElementById('logoutBtn').addEventListener('click', function() {
            // 세션 스토리지 초기화
            sessionStorage.clear();
            // 로컬 스토리지 초기화 (필요한 경우)
            localStorage.clear();
            // login.html로 리다이렉트
            window.location.href = 'login.html';
        });

    } catch (error) {
        console.error('초기화 중 오류 발생:', error);
    }
});
