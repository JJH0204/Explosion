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

// 로그아웃 함수 수정
async function logout() {
    try {
        const response = await fetch('assets/php/logout.php');
        const data = await response.json();
        if (data.success) {
            // 캐시 제거 및 히스토리 관리
            window.location.replace('login.html'); // replace를 사용하여 히스토리에서 현재 페이지 제거
        }
    } catch (error) {
        console.error('Logout failed:', error);
    }
}

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

    const uiManager = new UIManager();
    const gameManager = new GameManager();
    const cardManager = new CardManager(gameManager, uiManager);

    // 사용자 정보 업데이트 함수
    async function updateUserInfo() {
        try {
            const response = await fetch('assets/php/user_info.php');
            const data = await response.json();
            if (data.success) {
                document.getElementById('player-nickname').textContent = data.data.nickname;
                document.getElementById('current-level').textContent = data.data.rank;
                document.getElementById('player-score').textContent = data.data.score;
            } else {
                throw new Error(data.error || 'Failed to fetch user info');
            }
        } catch (error) {
            console.error('Failed to fetch user info:', error);
            if (error.message === 'User not logged in') {
                window.location.href = 'login.html';
            }
        }
    }

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

    // 로그아웃 버튼 이벤트 리스너 수정
    document.getElementById('logoutBtn').addEventListener('click', logout);

    // 이벤트 버튼 이벤트 리스너
    document.getElementById('EventBtn').addEventListener('click', () => {
        alert('이벤트 준비중입니다!');
    });
});
