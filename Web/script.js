/* script.js */
let currentPage = 0;
const cardsPerPage = 20;
const totalCards = 40;
const totalPages = Math.ceil(totalCards / cardsPerPage);
let collectedCount = 0; 

window.onload = function() {
    const challengeGrid = document.getElementById('challengeGrid');
    for (let p = 0; p < totalPages; p++) {
        const page = document.createElement('div');
        page.className = 'page';
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
            img.alt = `카드 ${i} 해결된 이미지`;
            cardBack.appendChild(img);
            
            cardInner.appendChild(cardFront);
            cardInner.appendChild(cardBack);
            card.appendChild(cardInner);
            page.appendChild(card);
        }
        challengeGrid.appendChild(page);
    }
};

function showPage(pageIndex) {
    const challengeGrid = document.getElementById('challengeGrid');
    challengeGrid.style.transform = `translateX(-${pageIndex * 100}vw)`;
}

function nextPage() {
    currentPage = Math.min(currentPage + 1, totalPages - 1);
    showPage(currentPage);
}

function prevPage() {
    currentPage = Math.max(currentPage - 1, 0);
    showPage(currentPage);
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
        cardBack.innerHTML = `<img src="./img/monster_image${cardId}.png" alt="몬스터 이미지" style="width:100%; height:100%;">`;

        collectedCount++;
        document.getElementById('collectedText').innerText = `${collectedCount} / ${totalCards}`;

        const progressFill = document.getElementById('progressFill');
        progressFill.style.width = `${(collectedCount / totalCards) * 100}%`;
    }

    closePopup();
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const leftArrowButton = document.querySelector('.arrow-button.left');
    sidebar.classList.toggle('active');
    
    if (sidebar.classList.contains('active')) {
        sidebarToggle.style.left = '270px'; // 사이드바 열렸을 때 토글 버튼 위치
        leftArrowButton.style.left = '270px'; // 사이드바 열렸을 때 왼쪽 화살표 버튼 위치
    } else {
        sidebarToggle.style.left = '20px'; // 사이드바 닫혔을 때 토글 버튼 위치
        leftArrowButton.style.left = '20px'; // 사이드바 닫혔을 때 왼쪽 화살표 버튼 위치
    }
}



function logout() {
    alert("로그아웃되었습니다.");
    window.location.href = "login.html"; 
}
