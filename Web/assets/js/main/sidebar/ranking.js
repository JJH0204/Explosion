// 점수 및 레벨 업데이트 함수
function updateScore() {
    fetch("./assets/php/Scoreboard2.php")
        .then(response => response.json())
        .then(data => {
            const scoreElement = document.getElementById("score");
            const levelElement = document.getElementById("level");
            
            // null 체크 추가
            if (scoreElement) {
                scoreElement.textContent = data.score;
            }
            if (levelElement) {
                levelElement.textContent = data.stage;
            }
        })
        .catch(error => console.error("Error updating score:", error));
}
// 페이지 로드 시 점수 불러오기
document.addEventListener("DOMContentLoaded", () => {
    updateScore();
});

// 팝업 닫기 함수
function closePopup() {
    document.getElementById("gamePopup").style.display = "none";
}

// 게임 해결 함수 예시
function solveGame() {
    completeStage();  // 스테이지 완료 처리
    closePopup();
}

function updateRanking() {
    fetch("./assets/php/ranking.php")
        .then(response => response.json())
        .then(data => {
            const rankingList = document.getElementById("rankingList");
            rankingList.innerHTML = ""; // 기존 랭킹 초기화

            if (data.success && Array.isArray(data.rankings)) {
                data.rankings.forEach((player, index) => {
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

// 5초마다 랭킹 갱신
document.addEventListener("DOMContentLoaded", () => {
    updateRanking(); // 첫 로드 시 랭킹 표시
    setInterval(updateRanking, 180000); // 1분마다 업데이트
});

