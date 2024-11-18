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
                    listItem.innerHTML = `                    
                        <strong>${index + 1}위</strong>
                        <span>${player.nickname}</span>
                        <span>점수: ${player.score}</span>`;
                    rankingList.appendChild(listItem);
                });
            } else {
                console.error("Unexpected data format or no rankings available:", data);
            }
        })
        .catch(error => console.error("Error fetching ranking data:", error));
}

// 5초마다 랭킹 갱신
document.addEventListener("DOMContentLoaded", () => {
    updateRanking(); // 첫 로드 시 랭킹 표시
    setInterval(updateRanking, 5000); // 5초마다 업데이트
});

