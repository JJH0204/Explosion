function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('active');
}

// 페이지 로드 시 40개의 카드를 동적으로 생성
window.onload = function() {
    const challengeGrid = document.getElementById('challengeGrid');
    
    for (let p = 0; p < 2; p++) {  // 2개의 페이지 생성 (각 페이지에 20장씩)
        const page = document.createElement('div');
        page.className = 'page';
        
        for (let i = 1 + p * 20; i <= 20 * (p + 1); i++) {
            const card = document.createElement('div');
            card.className = 'card';
            card.dataset.id = i;
            card.onclick = () => revealGame(card, `game${i}`);
            
            const cardFront = document.createElement('div');
            cardFront.className = 'card-front';
            cardFront.innerText = i;
            
            const cardBack = document.createElement('div');
            cardBack.className = 'card-back';
            const img = document.createElement('img');
            img.src = `image${i}.jpg`;
            img.alt = "해결된 이미지";
            cardBack.appendChild(img);
            
            card.appendChild(cardFront);
            card.appendChild(cardBack);
            page.appendChild(card);
        }
        
        challengeGrid.appendChild(page);
    }
};

function revealGame(cardElement, gameId) {
    // 게임 팝업 표시
    const popup = document.getElementById('gamePopup');
    popup.style.display = 'flex';

    // gameId에 따라 게임 내용을 로드
    document.getElementById('gameContent').innerText = `게임: ${gameId}`;
    
    // 현재 해결할 카드 설정 (cardElement의 ID 값을 popup에 저장)
    popup.dataset.currentCardId = cardElement.dataset.id;
}

function closePopup() {
    document.getElementById('gamePopup').style.display = 'none';
}

function solveGame() {
    // 현재 카드 요소 가져오기 (dataset을 통해 찾기)
    const popup = document.getElementById('gamePopup');
    const cardId = popup.dataset.currentCardId;
    const card = document.querySelector(`[data-id="${cardId}"]`);

    if (card) {
        // 카드에 해결 클래스 추가
        card.classList.add('solved');

        // 카드의 뒷면 이미지 변경 (해당 카드 번호에 맞는 monster 이미지로 설정)
        const cardBack = card.querySelector('.card-back');
        cardBack.innerHTML = `<img src="monster_image${cardId}.jpg" alt="몬스터 이미지" style="width:100%; height:100%;">`;
    }

    // 팝업 닫기
    closePopup();
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const container = document.querySelector('.container');
    const challengeGrid = document.querySelector('.challenge-grid');
    
    sidebar.classList.toggle('active');
    
    if (sidebar.classList.contains('active')) {
        container.style.marginLeft = '250px';
        challengeGrid.style.width = 'calc(100vw - 250px)';
    } else {
        container.style.marginLeft = '0';
        challengeGrid.style.width = '100vw';
    }
}

// 기존 코드 유지
