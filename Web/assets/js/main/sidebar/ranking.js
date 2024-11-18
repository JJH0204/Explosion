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

            // data.rankings가 배열인지 확인하고 순회
            if (data.success && Array.isArray(data.rankings)) {
                data.rankings.forEach((player, index) => {
                    const listItem = document.createElement("li");
                    // 랭킹에 따른 스타일 적용
                    let rankStyle = '';
                    if (index === 0) {
                        rankStyle = 'color: #FFD700;'; // 금색
                    } else if (index === 1) {
                        rankStyle = 'color: #C0C0C0;'; // 은색
                    } else if (index === 2) {
                        rankStyle = 'color: #CD7F32;'; // 동색
                    }
                    
                    listItem.innerHTML = `                    
                        <strong style="${rankStyle}">${index + 1}위</strong>
                        <span>${player.nickname}</span>
                        <span>점수: ${player.score} pt</span>`;
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

