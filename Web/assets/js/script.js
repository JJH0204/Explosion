let currentPage = 0;
const cardsPerPage = 20;
const totalCards = 40;
const totalPages = Math.ceil(totalCards / cardsPerPage);
let collectedCount = 0; // 현재까지 수집한 경험치
let userNickname = ""; 
let questionsData = null;

window.onload = function() {
    // 사용자 정보와 랭킹 정보를 가져옵니다.
    fetchUserInfo();
    fetchRanking();
    loadQuestionData();
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
            img.src = `./img/monster_image${i}.jpg`;
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

// 사용자 정보를 가져오는 함수 추가
function fetchUserInfo() {
    fetch('./php/user_info.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Fetched user info:', data); // 사용자 정보 로그
            if (data.username) {
                userNickname = data.username; // userNickname 변수에 사용자 이름 저장
                document.getElementById('user-nickname').innerText = userNickname; // 닉네임 업데이트
            }
        })
        .catch(error => {
            console.error('Error fetching user info:', error);
        });
}

function fetchRanking() {
    fetch('./php/ranking.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayRanking(data.rankings);
            } else {
                console.error('Error fetching rankings:', data.error);
            }
        })
        .catch(error => {
            console.error('Error fetching ranking:', error);
        });
}

function displayRanking(rankings) {
    const rankingList = document.getElementById('rankingList');
    rankingList.innerHTML = ''; // 기존 내용을 지웁니다.

    rankings.forEach((ranking, index) => {
        const listItem = document.createElement('li');
        listItem.textContent = `${index + 1}. ${ranking.username} - 점수: ${ranking.score}, 스테이지: ${ranking.stage}`;
        rankingList.appendChild(listItem);
    });
}

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
    
    // Create game content
    const gameContent = document.getElementById('gameContent');
    gameContent.innerHTML = `
      <h2>${gameId}</h2>
      <div class="game-description">
        <h3>게임 설명:</h3>
        <div id="gameDescription"></div>
      </div>
      <div class="flag-input">
        <label for="flagInput">플래그:</label>
        <input type="text" id="flagInput" placeholder="플래그를 입력하세요">
      </div>
      <div class="game-buttons">
        <button onclick="startChallenge('${gameId}')" class="challenge-button">도전하기</button>
        <button onclick="submitFlag('${gameId}')" class="submit-button">정답 제출</button>
      </div>
    `;
  
    // Fetch and display game description
    fetchGameDescription(gameId);
  
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

        // 경험치 증가
        collectedCount++;
        document.getElementById('collectedText').innerText = `${collectedCount} / ${totalCards}`;

        const progressFill = document.getElementById('progressFill');
        progressFill.style.width = `${(collectedCount / totalCards) * 100}%`; // 진행률 바 업데이트

        // 서버에 게임 요청 보내기
        fetch('./php/set_game_request.php', {
            method: 'POST',
            credentials: 'same-origin'
        })
        .then(response => {
            if (response.ok) {
                // 이후 점수 업데이트 요청
                fetchScoreUpdate();
            }
        })
        .catch(error => console.error('Error setting game request:', error));
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

function fetchScoreUpdate() {
    fetch('./php/Scoreboard2.php', {
        method: 'POST',
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error('Error:', data.error);
            alert(data.error); // 사용자에게 오류 메시지 표시
        } else if (data.message) {
            console.log(data.message);
            updateScoreboard(data.score, data.stage);
        }
    })
    .catch(error => console.error('Error parsing JSON:', error));
}

function logout() {
    alert("로그아웃되었습니다.");
    window.location.href = "login.html"; 
}

// JSON 데이터를 로드하는 함수
function loadQuestionData() {
    fetch('./json/data.json')
      .then(response => response.json())
      .then(data => {
        questionsData = data;
        console.log('Questions data loaded');
      })
      .catch(error => console.error('Error loading questions data:', error));
  }
  
  // ... existing code ...
  
  function submitFlag(gameId) {
    const flagInput = document.getElementById('flagInput');
    const flag = flagInput.value.trim();
    if (!flag) {
      alert("플래그를 입력해주세요.");
      return;
    }
  
    if (!questionsData) {
      alert("문제 데이터가 로드되지 않았습니다. 잠시 후 다시 시도해주세요.");
      return;
    }
  
    console.log(`Submitting flag for ${gameId}: ${flag}`);
    alert("플래그가 제출되었습니다. 검증 중...");
  
    // gameId에서 문제 번호 추출 (예: 'game1' -> 0)
    const questionIndex = parseInt(gameId.replace('game', '')) - 1;
    
    if (questionIndex < 0 || questionIndex >= questionsData.QCount) {
      alert("유효하지 않은 게임 ID입니다.");
      return;
    }
  
    const correctAnswer = questionsData.QData[questionIndex].answer;
  
    setTimeout(() => {
      if (flag === correctAnswer) {
        alert("축하합니다! 정답입니다.");
        solveGame();
      } else {
        alert("틀렸습니다. 다시 시도해주세요.");
      }
    }, 1000);
  }
  
  // ... existing code ...
  
  function fetchGameDescription(gameId) {
    if (!questionsData) {
      console.error('Questions data not loaded');
      return;
    }
  
    const questionIndex = parseInt(gameId.replace('game', '')) - 1;
    if (questionIndex < 0 || questionIndex >= questionsData.QCount) {
      console.error('Invalid game ID');
      return;
    }
  
    const descPath = questionsData.QData[questionIndex].desc;
    fetch(descPath)
      .then(response => response.text())
      .then(markdown => {
        const gameDescriptionElement = document.getElementById('gameDescription');
        if (gameDescriptionElement) {
          if (typeof marked === 'function') {
            gameDescriptionElement.innerHTML = marked(markdown);
          } else {
            gameDescriptionElement.textContent = markdown;
          }
        } else {
          console.error('gameDescription element not found');
        }
      })
      .catch(error => {
        console.error('Error fetching game description:', error);
        document.getElementById('gameDescription').textContent = "게임 설명을 불러오는 데 실패했습니다.";
      });
  }

    
function startChallenge(gameId) {
    
     // Extract the number from the gameId (e.g., 'game1' -> '01')
    
    const gameNumber = gameId.replace('game', '').padStart(2, '0');
    
    
    
     // Construct the challenge URL
    
    const challengeUrl = `/Question/${gameNumber}.html`;
    
    

     // Open the URL in a new tab
    
     window.open(challengeUrl, '_blank');
    
    }
  
async function loadChallengeData() {
    try {
        const response = await fetch('json/data.json');
        const data = await response.json();
        return data.challenges;
    } catch (error) {
        console.error('챌린지 데이터를 불러오는데 실패했습니다:', error);
        return [];
    }
}

async function loadMarkdownContent(markdownFile) {
    try {
        const response = await fetch(`markdown/${markdownFile}`);
        const markdownContent = await response.text();
        return marked.parse(markdownContent); // 마크다운을 HTML로 변환
    } catch (error) {
        console.error('마크다운 파일을 불러오는데 실패했습니다:', error);
        return '문제 내용을 불러올 수 없습니다.';
    }
}
async function showChallenge(challengeId) {
    try {
        // 경로를 Web 디렉토리 기준으로 수정
        const response = await fetch(`./markdown/mk_${challengeId}.md`);
        if (!response.ok) {
            throw new Error('파일을 찾을 수 없습니다.');
        }
        const markdownContent = await response.text();
        
        // marked 옵션 설정 추가
        marked.setOptions({
            breaks: true,  // 줄바꿈 허용
            gfm: true      // GitHub Flavored Markdown 활성화
        });
        
        const gameContent = document.getElementById('gameContent');
        // HTML 문자열을 이스케이프하지 않도록 설정
        gameContent.innerHTML = marked.parse(markdownContent);
        
        const gamePopup = document.getElementById('gamePopup');
        gamePopup.setAttribute('aria-hidden', 'false');
        gamePopup.style.display = 'block';
    } catch (error) {
        console.error('문제 설명을 불러오는데 실패했습니다:', error);
        const gameContent = document.getElementById('gameContent');
        gameContent.innerHTML = '문제 설명을 불러올 수 없습니다.';
    }
}
