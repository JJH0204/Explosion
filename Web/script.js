// script.js

let currentPage = 0;
const cardsPerPage = 20;
const totalCards = 40;
const totalPages = Math.ceil(totalCards / cardsPerPage);
let collectedCount = 0; // 카드 수집 카운트

// 페이지 로드 시 카드 생성
window.onload = function() {
    const challengeGrid = document.getElementById('challengeGrid');
    
    for (let p = 0; p < totalPages; p++) {
        const page = document.createElement('div');
        page.className = 'page';
        page.style.display = p === 0 ? 'grid' : 'none';
        
        for (let i = 1 + p * cardsPerPage; i <= cardsPerPage * (p + 1) && i <= totalCards; i++) {
            const card = document.createElement('div');
            card.className = 'card';
            card.dataset.id = i;
            card.onclick = () => revealGame(card, `game${i}`);
            
            const cardInner = document.createElement('div');
            cardInner.className = 'card-inner';
            
            const cardFront = document.createElement('div');
            cardFront.className = 'card-front';
            cardFront.innerText = i;
            
            const cardBack = document.createElement('div');
            cardBack.className = 'card-back';
            const img = document.createElement('img');
            img.src = `./img/image${i}.jpg`;
            img.alt = "해결된 이미지";
            cardBack.appendChild(img);
            
            cardInner.appendChild(cardFront);
            cardInner.appendChild(cardBack);
            card.appendChild(cardInner);
            page.appendChild(card);
        }
        
        challengeGrid.appendChild(page);
    }
};

// 페이지 이동 함수
function showPage(pageIndex) {
    const pages = document.querySelectorAll('.page');
    pages.forEach((page, index) => {
        page.style.display = index === pageIndex ? 'grid' : 'none';
    });
}

// 다음 페이지로 이동
function nextPage() {
    if (currentPage < totalPages - 1) {
        currentPage++;
        showPage(currentPage);
    }
}

// 이전 페이지로 이동
function prevPage() {
    if (currentPage > 0) {
        currentPage--;
        showPage(currentPage);
    }
}

function revealGame(cardElement, gameId) {
    const popup = document.getElementById('gamePopup');
    popup.style.display = 'flex';
    document.getElementById('gameContent').innerText = `게임: ${gameId}`;
    popup.dataset.currentCardId = cardElement.dataset.id;
}

function closePopup() {
    document.getElementById('gamePopup').style.display = 'none';
}

function solveGame() {
    const popup = document.getElementById('gamePopup');
    const cardId = popup.dataset.currentCardId;
    const card = document.querySelector(`[data-id="${cardId}"]`);

    if (card && !card.classList.contains('solved')) {
        card.classList.add('solved');
        const cardInner = card.querySelector('.card-inner');
        cardInner.style.transform = "rotateY(180deg)";
        
        const cardBack = card.querySelector('.card-back');
        cardBack.innerHTML = `<img src="./img/monster_image${cardId}.jpg" alt="몬스터 이미지" style="width:100%; height:100%;">`;

        // 카드 수집기 업데이트
        collectedCount++;
        document.getElementById('collectedText').innerText = `${collectedCount} / ${totalCards}`;

        // 진행 상태 바 업데이트
        const progressFill = document.getElementById('progressFill');
        progressFill.style.width = `${(collectedCount / totalCards) * 100}%`;
    }

    closePopup();
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}
// 점수 및 레벨 업데이트 함수
function updateScore() {
    fetch("Scoreboard2.php")
        .then(response => response.json())
        .then(data => {
            document.getElementById("score").textContent = data.score;
            document.getElementById("level").textContent = data.stage;
        })
        .catch(error => console.error("Error updating score:", error));
}

// 페이지 로드 시 점수 불러오기
document.addEventListener("DOMContentLoaded", () => {
    updateScore();
});

// 스테이지 완료 시 호출하는 함수 예시
function completeStage() {
    updateScore(10);  // 스테이지 완료 시 점수 10점 추가
    alert("스테이지 완료! 점수가 갱신되었습니다.");
}

// 팝업 닫기 함수
function closePopup() {
    document.getElementById("gamePopup").style.display = "none";
}

// 게임 해결 함수 예시
function solveGame() {
    completeStage();  // 스테이지 완료 처리
    closePopup();
}
// 랭킹 정보를 업데이트하는 함수
function updateRanking() {
    fetch("ranking.php")
        .then(response => response.json())
        .then(data => {
            const rankingList = document.getElementById("rankingList");
            rankingList.innerHTML = ""; // 기존 랭킹 초기화

            // 랭킹 데이터를 순회하며 리스트 생성
            data.forEach((player, index) => {
                const listItem = document.createElement("li");
                listItem.innerHTML = `                    
                    <strong>${index + 1}위</strong>
                    <span>${player.username}</span>
                    <span>점수: ${player.score}</span>
                    <span>레벨: ${player.stage}</span>`;
                rankingList.appendChild(listItem);
            });
        })
        .catch(error => console.error("Error fetching ranking data:", error));
}

// 5초마다 랭킹 갱신
document.addEventListener("DOMContentLoaded", () => {
    updateRanking(); // 첫 로드 시 랭킹 표시
    setInterval(updateRanking, 5000); // 5초마다 업데이트
});
