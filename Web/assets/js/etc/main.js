document.addEventListener('DOMContentLoaded', async () => {
    // 전역 게임 UI 인스턴스 생성
    window.gameUI = null;

    // 뒤로가기 방지 및 세션 관리
    initializeNavigationControl();
    
    // 로그인 상태 체크
    const isLoggedIn = await checkLoginStatus();
    if (!isLoggedIn) return;

    // GameUI 초기화
    window.gameUI = new GameUI();

    // 이벤트 리스너 초기화
    initializeEventListeners();

    // 초기 데이터 로드 및 주기적 업데이트 설정
    await initializeUserData();
    startPeriodicUpdates();
});

function initializeNavigationControl() {
    window.history.pushState(null, null, window.location.href);
    window.onpopstate = async function () {
        window.history.pushState(null, null, window.location.href);
        const isSessionValid = await checkSession();
        if (!isSessionValid) {
            redirectToLogin();
        }
    };
}

async function checkSession() {
    try {
        const response = await fetch('assets/php/checkSession.php');
        const data = await response.json();
        return data.success;
    } catch (error) {
        console.error('세션 체크 실패:', error);
        return false;
    }
}

async function checkLoginStatus() {
    try {
        const response = await fetch('assets/php/checkSession.php');
        const data = await response.json();
        if (!data.success) {
            redirectToLogin();
            return false;
        }
        return true;
    } catch (error) {
        console.error('로그인 상태 확인 실패:', error);
        redirectToLogin();
        return false;
    }
}

function redirectToLogin() {
    window.location.replace('index.html');
}

async function updateUserInfo() {
    try {
        const response = await fetch('assets/php/user_info.php');
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || '사용자 정보를 가져오는데 실패했습니다');
        }

        updateUserInfoDisplay(data.data);
    } catch (error) {
        console.error('사용자 정보 업데이트 실패:', error);
        if (error.message === 'User not logged in') {
            redirectToLogin();
        }
    }
}

function updateUserInfoDisplay(userData) {
    document.getElementById('player-nickname').textContent = userData.nickname;
    document.getElementById('current-level').textContent = userData.rank;
    document.getElementById('player-score').textContent = userData.score;
}

async function updateRanking() {
    try {
        const response = await fetch('assets/php/ranking.php');
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || '랭킹 정보를 가져오는데 실패했습니다');
        }

        updateRankingDisplay(data.rankings);
    } catch (error) {
        console.error('랭킹 업데이트 실패:', error);
    }
}

function updateRankingDisplay(rankings) {
    const rankingList = document.getElementById('rankingList');
    rankingList.innerHTML = rankings.map((player, index) => `
        <li>
            <strong>${index + 1}위</strong>
            <span>${player.nickname}</span>
            <span>점수: ${player.score} pt</span>
        </li>
    `).join('');
}

async function initializeUserData() {
    await Promise.all([
        updateUserInfo(),
        updateRanking()
    ]);
}

function startPeriodicUpdates() {
    const UPDATE_INTERVAL = 30000; // 30초
    setInterval(async () => {
        await Promise.all([
            updateUserInfo(),
            updateRanking()
        ]);
    }, UPDATE_INTERVAL);
}

function initializeEventListeners() {
    // 로그아웃 버튼
    document.getElementById('logoutBtn').addEventListener('click', handleLogout);
    
    // 이벤트 버튼
    document.getElementById('EventBtn').addEventListener('click', () => {
        alert('이벤트 준비중입니다!');
    });
}

async function handleLogout() {
    try {
        const response = await fetch('assets/php/logout.php');
        const data = await response.json();
        
        if (data.success) {
            redirectToLogin();
        } else {
            throw new Error(data.error || '로그아웃 실패');
        }
    } catch (error) {
        console.error('로그아웃 처리 실패:', error);
    }
}
