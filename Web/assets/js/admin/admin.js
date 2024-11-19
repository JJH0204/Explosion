class AdminCardManager {
    constructor() {
        this.currentPage = 0;
        this.cardsPerPage = 10;
        this.totalCards = 40;
        this.totalPages = Math.ceil(this.totalCards / this.cardsPerPage);
        this.init();
    }

    init() {
        const cardsWrapper = document.querySelector('.cards-wrapper');
        if (!cardsWrapper) {
            console.error('Cards wrapper not found!');
            return;
        }
        
        cardsWrapper.innerHTML = '';

        // 페이지별로 그리드 생성
        for (let page = 0; page < this.totalPages; page++) {
            const grid = document.createElement('div');
            grid.className = 'cards-grid';
            
            // 각 페이지의 카드 생성
            const startCard = page * this.cardsPerPage + 1;
            const endCard = Math.min((page + 1) * this.cardsPerPage, this.totalCards);
            
            for (let i = startCard; i <= endCard; i++) {
                const card = document.createElement('div');
                card.className = 'card';
                
                const img = document.createElement('img');
                img.src = `assets/images/monsters/monster_image${i}.png`;
                img.alt = `Monster ${i}`;
                
                card.appendChild(img);
                grid.appendChild(card);
            }
            
            cardsWrapper.appendChild(grid);
        }

        console.log('Cards initialized:', cardsWrapper.children.length, 'pages');
    }

    updateVisibility() {
        const grid = document.getElementById('challengeGrid');
        const startIdx = this.currentPage * this.cardsPerPage;
        const endIdx = startIdx + this.cardsPerPage;
        
        Array.from(grid.children).forEach((card, index) => {
            card.style.display = index >= startIdx && index < endIdx ? 'block' : 'none';
        });
    }

    nextPage() {
        if (this.currentPage < this.totalPages - 1) {
            this.currentPage++;
            this.updateVisibility();
        }
    }

    prevPage() {
        if (this.currentPage > 0) {
            this.currentPage--;
            this.updateVisibility();
        }
    }
}

// 점수 업데이트 함수
function updateScore() {
    fetch("./assets/php/Scoreboard2.php")
        .then(response => response.json())
        .then(data => {
            const scoreElement = document.getElementById("score");
            const levelElement = document.getElementById("level");
            
            if (scoreElement) scoreElement.textContent = data.score;
            if (levelElement) levelElement.textContent = data.stage;
        })
        .catch(error => console.error("Error updating score:", error));
}

// 랭킹 업데이트 함수 수정
function updateRanking() {
    fetch("./assets/php/ranking.php")
        .then(response => response.json())
        .then(data => {
            const rankingList = document.getElementById("rankingList");
            if (!rankingList) return;
            
            rankingList.innerHTML = "";

            if (data.success && Array.isArray(data.rankings)) {
                // 상위 9명만 표시하도록 slice 메서드 사용
                data.rankings.slice(0, 9).forEach((player, index) => {
                    const listItem = document.createElement("li");
                    listItem.className = 'ranking-item';
                    
                    // 랭킹 아이콘 또는 숫자 결정
                    let rankDisplay;
                    if (index === 0) {
                        rankDisplay = '🥇';
                    } else if (index === 1) {
                        rankDisplay = '🥈';
                    } else if (index === 2) {
                        rankDisplay = '🥉';
                    } else {
                        rankDisplay = `${index + 1}`;
                    }
                    
                    listItem.innerHTML = `
                        <div class="rank">${rankDisplay}</div>
                        <div class="player-info">
                            <span class="nickname">${player.nickname}</span>
                            <span class="score">${player.score}pt</span>
                        </div>
                    `;
                    rankingList.appendChild(listItem);
                });
            }
        })
        .catch(error => console.error("Error fetching ranking data:", error));
}

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

// 로그아웃 함수
async function logout() {
    try {
        const response = await fetch('assets/php/logout.php');
        const data = await response.json();
        if (data.success) {
            window.location.replace('login.html');
        }
    } catch (error) {
        console.error('Logout failed:', error);
    }
}

// 팝업 관련 함수들
function closePopup() {
    document.getElementById("gamePopup").style.display = "none";
}

function solveGame() {
    completeStage();
    closePopup();
}

// 초기화 코드
document.addEventListener('DOMContentLoaded', async () => {
    // 뒤로가기 방지
    window.history.pushState(null, null, window.location.href);
    window.onpopstate = async function () {
        window.history.pushState(null, null, window.location.href);
        const isLoggedIn = await checkLoginStatus();
        if (!isLoggedIn) window.location.replace('login.html');
    };

    // 초기화
    const isLoggedIn = await checkLoginStatus();
    if (!isLoggedIn) return;

    // 카드 매니저 초기화 및 전역 변수로 설정
    window.cardManager = new AdminCardManager();  // 화살표 버튼에서 접근할 수 있도록 전역으로 설정

    // 화살표 버튼 이벤트 리스너 추가
    document.querySelector('.arrow-button.left').addEventListener('click', () => window.cardManager.prevPage());
    document.querySelector('.arrow-button.right').addEventListener('click', () => window.cardManager.nextPage());

    // 이벤트 리스너 등록
    document.getElementById('logoutBtn').addEventListener('click', logout);
    document.getElementById('EventBtn').addEventListener('click', () => {
        const eventPopup = document.getElementById('eventPopup');
        eventPopup.style.display = 'flex';
    });

    // 이벤트 팝업 닫기 버튼
    document.getElementById('closeEvent').addEventListener('click', () => {
        document.getElementById('eventPopup').style.display = 'none';
    });

    // 초기 데이터 로드
    updateScore();
    updateRanking();

    // 주기적 업데이트
    setInterval(updateRanking, 180000); // 3분마다 랭킹 업데이트
});

// Footer 토글 기능
document.addEventListener('DOMContentLoaded', function() {
    const footerToggle = document.querySelector('.footer-toggle');
    const footer = document.querySelector('.footer');
    
    footerToggle.addEventListener('click', function() {
        footer.classList.toggle('show');
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const leftButton = document.querySelector('.arrow-button.left');
    const rightButton = document.querySelector('.arrow-button.right');
    const cardsWrapper = document.querySelector('.cards-wrapper');
    
    let currentPage = 0;
    const totalPages = 4; // 전체 페이지 수

    // 페이지 이동 함수
    function movePage(direction) {
        if (direction === 'right' && currentPage < totalPages - 1) {
            currentPage++;
            cardsWrapper.style.transform = `translateX(-${currentPage * 100}%)`;
        } else if (direction === 'left' && currentPage > 0) {
            currentPage--;
            cardsWrapper.style.transform = `translateX(-${currentPage * 100}%)`;
        }
        
        updateButtonStates();
    }

    // 버튼 상태 업데이트
    function updateButtonStates() {
        leftButton.style.opacity = currentPage === 0 ? '0.5' : '1';
        leftButton.style.pointerEvents = currentPage === 0 ? 'none' : 'auto';
        
        rightButton.style.opacity = currentPage === totalPages - 1 ? '0.5' : '1';
        rightButton.style.pointerEvents = currentPage === totalPages - 1 ? 'none' : 'auto';
    }

    // 버튼 이벤트 리스너
    rightButton.addEventListener('click', () => movePage('right'));
    leftButton.addEventListener('click', () => movePage('left'));

    // 초기 버튼 상태 설정
    updateButtonStates();
}); 