document.addEventListener('DOMContentLoaded', async () => {
    // 뒤로가기 방지
    window.history.pushState(null, null, window.location.href);
    window.onpopstate = async function () {
        window.history.pushState(null, null, window.location.href);
        // 세션 체크
        const response = await fetch('assets/php/checkSession.php');
        const data = await response.json();
        if (!data.success) {
            window.location.replace('login.html');
        }
    };

    // 로그인 상태 체크
    const isLoggedIn = await checkLoginStatus();
    if (!isLoggedIn) return;

    // Admin 카드 매니저 초기화 (GameManager와 UIManager 불필요)
    const cardManager = new AdminCardManager();

    // 랭킹 업데이트 함수
    async function updateRanking() {
        try {
            const response = await fetch('assets/php/ranking.php');
            const data = await response.json();
            if (data.success) {
                const rankingList = document.getElementById('rankingList');
                rankingList.innerHTML = '';
                data.rankings.forEach((player, index) => {
                    const listItem = document.createElement('li');
                    listItem.innerHTML = `
                        <strong>${index + 1}위</strong>
                        <span>${player.nickname}</span>
                        <span>점수: ${player.score} pt</span>`;
                    rankingList.appendChild(listItem);
                });
            }
        } catch (error) {
            console.error('Failed to fetch ranking:', error);
        }
    }

    // 로그아웃 버튼 이벤트 리스너
    document.getElementById('logoutBtn').addEventListener('click', logout);

    // 이벤트 버튼 이벤트 리스너
    document.getElementById('EventBtn').addEventListener('click', () => {
        alert('이벤트 준비중입니다!');
    });

    // 초기 랭킹 업데이트
    updateRanking();
    // 주기적 랭킹 업데이트 (30초마다)
    setInterval(updateRanking, 30000);
});

// 로그인 상태 체크 함수
async function checkLoginStatus() {
    try {
        const response = await fetch('assets/php/checkSession.php');
        const data = await response.json();
        if (!data.success) {
            window.location.href = 'login.html';
            return false;
        }
        return true;
    } catch (error) {
        console.error('Login check failed:', error);
        window.location.href = 'login.html';
        return false;
    }
} 